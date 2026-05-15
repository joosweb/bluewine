<?php

namespace App\Http\Controllers\Api;

use App\Services\LocalPrintAgentService;
use App\Services\PrintControlService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Date;
use Auth;
use DB;

/* A wrapper to do organise item names & prices into columns */
class item
{
    private $name;
    private $price;
    private $dollarSign;

    public function __construct($name = '', $price = '', $dollarSign = false)
    {
        $this -> name = $name;
        $this -> price = $price;
        $this -> dollarSign = $dollarSign;
    }

    public function __toString()
    {
        $rightCols = 10;
        $leftCols = 38;
        if ($this -> dollarSign) {
            $leftCols = $leftCols / 2 - $rightCols / 2;
        }
        $left = str_pad($this -> name, $leftCols) ;

        $sign = ($this -> dollarSign ? '$ ' : '');
        $right = str_pad($sign . $this -> price, $rightCols, ' ', STR_PAD_LEFT);
        return "$left$right\n";
    }
}

class VoucherController extends Controller
{
    private $userID;
    private $printControlService;
    private $localPrintAgentService;

    public function __construct(PrintControlService $printControlService, LocalPrintAgentService $localPrintAgentService) {
      $this->printControlService = $printControlService;
      $this->localPrintAgentService = $localPrintAgentService;
      $this->middleware('auth');
      $this->middleware(function ($request, $next) {
      if(Auth::user()->fk_id_user_type == 1)
      {
        $this->userID = Auth::id(); // IS ADMIN
      }
      else {
        $query = DB::table('shop')
        ->select('fk_user_id_admin')
        ->where('id', Auth::user()->shop)
        ->first();
        $this->userID = $query->fk_user_id_admin;
      }
      return $next($request);
      });
    }

    private function shopId()
    {
      return Auth::user()->shop;
    }

    private function buildPrintGrant($saleId, $mode, $source, $reason = null, $copyNumber = 0)
    {
      $token = (string) Str::uuid();
      Cache::put('voucher_print_grant_' . $token, [
        'sale_id' => (int) $saleId,
        'mode' => $mode,
        'source' => $source,
        'reason' => $reason,
        'copy_number' => (int) $copyNumber,
        'issued_to' => Auth::id(),
      ], now()->addMinutes(5));

      return $token;
    }

    private function consumePrintGrant($token, $saleId, $mode)
    {
      if (!$token) {
        return null;
      }

      $cacheKey = 'voucher_print_grant_' . $token;
      $grant = Cache::get($cacheKey);
      if (!$grant) {
        return null;
      }

      if ((int) $grant['sale_id'] !== (int) $saleId) {
        return null;
      }

      if ($grant['mode'] !== $mode) {
        return null;
      }

      if ((int) $grant['issued_to'] !== (int) Auth::id()) {
        return null;
      }

      return $grant;
    }

    private function resolvePhotoSrc($photo)
    {
      if (!$photo || empty($photo->photo)) {
        return null;
      }

      $rawPath = (string) $photo->photo;
      if (preg_match('/^https?:\/\//i', $rawPath)) {
        return $rawPath;
      }

      $relativePath = ltrim($rawPath, '/');
      $absolutePath = public_path($relativePath);
      if (file_exists($absolutePath)) {
        return $absolutePath;
      }

      return asset($rawPath);
    }

    private function buildVoucherData($id)
    {
      $sale = $this->printControlService->validateSaleOwnership($id, $this->shopId());
      if (!$sale) {
        throw new \Exception('Venta no encontrada');
      }

      $sales = [$sale];
      $sales_items = DB::table('sales_item')->where('fk_sales_id', $sale->id)->get()->toArray();
      $photo = DB::table('users')->select('photo')->where('id', $this->userID)->first();
      $config = DB::table('page_config')->where('fk_user_id', $this->userID)->first();
      $shop_data = DB::table('users')->where('id', $this->userID)
      ->select('name', 'last_name', 'phone', 'city', 'address')
      ->get()->toArray();

      $shop_name_eccomerce = DB::table('page_config')->select('name_ecommerce')->where('fk_user_id', $this->userID)->first();

      return compact('sales', 'sales_items', 'photo', 'config', 'shop_data', 'shop_name_eccomerce');
    }

    private function printMetaFromGrant($grant)
    {
      return [
        'is_reprint' => $grant['mode'] === 'reprint',
        'copy_number' => (int) $grant['copy_number'],
        'reason' => $grant['reason'],
        'printed_by' => Auth::user()->name . ' ' . Auth::user()->last_name,
        'printed_at' => Date::now()->format('Y-m-d H:i:s'),
      ];
    }

    private function streamVoucherPdf($data, $printMeta)
    {
      $sales = $data['sales'];
      $sales_items = $data['sales_items'];
      $photo = $data['photo'];
      $photoSrc = $this->resolvePhotoSrc($photo);
      $config = $data['config'];
      $shop_data = $data['shop_data'];
      $shop_name_eccomerce = $data['shop_name_eccomerce'];

      if ($config->voucher_logo) {
        $pdfWidth  = 250 + count($sales_items) * 70;
      } else {
        $pdfWidth  = 160 + count($sales_items) * 70;
      }

      $paperType = (int) $config->continuous_paper_type;

      if ($paperType === 58) {
        $pdfHeight = 140;
        $pdf = \PDF::loadView('invoice.voucher_58', compact('sales', 'sales_items', 'shop_data', 'shop_name_eccomerce', 'photo', 'photoSrc', 'config', 'printMeta'))->setPaper(array(0, 0, $pdfWidth, $pdfHeight), 'landscape');
        return $pdf->stream('archivo.pdf');
      }

      $pdfHeight = 210;
      $pdf = \PDF::loadView('invoice.voucher_80', compact('sales', 'sales_items', 'shop_data', 'shop_name_eccomerce', 'photo', 'photoSrc', 'config', 'printMeta'))->setPaper(array(0, 0, $pdfWidth, $pdfHeight), 'landscape');
      return $pdf->stream('archivo.pdf');
    }

    private function buildVoucherPdfBinary($data, $printMeta)
    {
      $sales = $data['sales'];
      $sales_items = $data['sales_items'];
      $photo = $data['photo'];
      $photoSrc = $this->resolvePhotoSrc($photo);
      $config = $data['config'];
      $shop_data = $data['shop_data'];
      $shop_name_eccomerce = $data['shop_name_eccomerce'];

      if ($config->voucher_logo) {
        $pdfWidth  = 250 + count($sales_items) * 70;
      } else {
        $pdfWidth  = 160 + count($sales_items) * 70;
      }

      $paperType = (int) $config->continuous_paper_type;

      if ($paperType === 58) {
        $pdfHeight = 140;
        $pdf = \PDF::loadView('invoice.voucher_58', compact('sales', 'sales_items', 'shop_data', 'shop_name_eccomerce', 'photo', 'photoSrc', 'config', 'printMeta'))->setPaper(array(0, 0, $pdfWidth, $pdfHeight), 'landscape');
        return $pdf->output();
      }

      $pdfHeight = 210;
      $pdf = \PDF::loadView('invoice.voucher_80', compact('sales', 'sales_items', 'shop_data', 'shop_name_eccomerce', 'photo', 'photoSrc', 'config', 'printMeta'))->setPaper(array(0, 0, $pdfWidth, $pdfHeight), 'landscape');
      return $pdf->output();
    }

    private function toAscii($text)
    {
      $clean = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', (string) $text);
      if ($clean === false) {
        $clean = (string) $text;
      }
      return preg_replace('/[^\x20-\x7E]/', '', $clean);
    }

    private function padRight($text, $size)
    {
      $text = substr($this->toAscii($text), 0, $size);
      return str_pad($text, $size, ' ', STR_PAD_RIGHT);
    }

    private function padLeft($text, $size)
    {
      $text = substr($this->toAscii($text), 0, $size);
      return str_pad($text, $size, ' ', STR_PAD_LEFT);
    }

    private function buildEscPosPayload($data, $printMeta)
    {
      $sales = $data['sales'];
      $sales_items = $data['sales_items'];
      $photo = $data['photo'];
      $config = $data['config'];
      $shop_data = $data['shop_data'];
      $shop_name_eccomerce = $data['shop_name_eccomerce'];

      $paperType = (int) $config->continuous_paper_type;
      $is58mm = ($paperType === 58);
      $lineWidth = $is58mm ? 32 : 42;
      $qtyWidth = $is58mm ? 4 : 6;
      $priceWidth = $is58mm ? 10 : 12;
      $productWidth = $lineWidth - $qtyWidth - $priceWidth;

      $amount = (int) $sales[0]->amount;
      $discount = (int) $sales[0]->discount;
      $discValue = (int) (round($discount * $amount / 100 / 10) * 10);
      $total = $amount - $discValue;

      $text = '';
      $text .= "\x1B@";
      // Use standard Font A for slightly larger and more readable text.
      $text .= "\x1BM\x00";
      $text .= "\x1Ba\x01";
      if (!empty($config->voucher_logo)) {
        $logoMaxWidth = $is58mm ? 192 : 256;
        $logoMaxHeight = $is58mm ? 80 : 110;
        $logoBytes = $this->buildEscPosLogoBytes($this->resolvePhotoSrc($photo), $logoMaxWidth, $logoMaxHeight);
        if ($logoBytes !== '') {
          $text .= $logoBytes;
          $text .= "\n";
        }
      }
      $text .= $this->toAscii(strtoupper($shop_name_eccomerce->name_ecommerce)) . "\n";
      $text .= $this->toAscii(strtoupper($shop_data[0]->address)) . "\n";
      $text .= $this->toAscii($shop_data[0]->phone) . " | " . $this->toAscii(date('d/m/Y H:i', strtotime($sales[0]->created_at))) . "\n";
      if (!empty($sales[0]->folio)) {
        $text .= 'DOC N: ' . $this->toAscii($sales[0]->folio) . "\n";
      }
      if (!empty($printMeta['is_reprint'])) {
        $text .= '*** REIMPRESION COPIA ' . (int) $printMeta['copy_number'] . " ***\n";
      }
      $text .= str_repeat('-', $lineWidth) . "\n";
      $text .= $this->padRight('C', $qtyWidth) . $this->padRight('PRODUCTO', $productWidth) . $this->padLeft('$', $priceWidth) . "\n";
      $text .= str_repeat('-', $lineWidth) . "\n";

      foreach ($sales_items as $value) {
        $qty = $this->padRight($value->quantity . 'x', $qtyWidth);
        $name = $this->padRight(strtoupper($value->name_item), $productWidth);
        $price = $this->padLeft('$' . number_format((int) $value->price), $priceWidth);
        $text .= $qty . $name . $price . "\n";
      }

      $text .= str_repeat('-', $lineWidth) . "\n";
      if ($discount > 0) {
        $text .= $this->padRight('DESCUENTO', $lineWidth - $priceWidth) . $this->padLeft('$' . number_format($discValue), $priceWidth) . "\n";
      }
      $text .= $this->padRight('TOTAL', $lineWidth - $priceWidth) . $this->padLeft('$' . number_format($total), $priceWidth) . "\n";
      $text .= str_repeat('-', $lineWidth) . "\n";
      if (!empty($printMeta['is_reprint'])) {
        $text .= 'MOTIVO: ' . $this->toAscii($printMeta['reason']) . "\n";
      }
      $text .= 'POR: ' . $this->toAscii($printMeta['printed_by']) . "\n";
      $text .= "\x1Ba\x01";
      $text .= 'GRACIAS POR SU COMPRA' . "\n";
      // Feed extra lines to avoid cutting over the footer text.
      $text .= "\x1Bd\x05";
      $text .= "\x1DV\x00";

      return $text;
    }

    private function buildEscPosLogoBytes($photoSrc, $maxWidthPx = 384, $maxHeightPx = 220)
    {
      if (!$photoSrc || !function_exists('imagecreatefromstring')) {
        return '';
      }

      $imageBytes = @file_get_contents($photoSrc);
      if ($imageBytes === false) {
        return '';
      }

      $img = @imagecreatefromstring($imageBytes);
      if ($img === false) {
        return '';
      }

      $srcW = imagesx($img);
      $srcH = imagesy($img);
      if ($srcW <= 0 || $srcH <= 0) {
        imagedestroy($img);
        return '';
      }

      $targetW = min($srcW, (int) $maxWidthPx);
      $scale = $targetW / $srcW;
      $targetH = max(1, (int) floor($srcH * $scale));

      // Bound logo height for thermal printers to keep payload small.
      $targetH = min($targetH, (int) $maxHeightPx);
      $targetW = max(1, min($targetW, 384));

      $resized = imagecreatetruecolor($targetW, $targetH);
      $white = imagecolorallocate($resized, 255, 255, 255);
      imagefilledrectangle($resized, 0, 0, $targetW, $targetH, $white);
      imagecopyresampled($resized, $img, 0, 0, 0, 0, $targetW, $targetH, $srcW, $srcH);
      imagedestroy($img);

      $widthBytes = (int) ceil($targetW / 8);
      $xL = chr($widthBytes & 0xFF);
      $xH = chr(($widthBytes >> 8) & 0xFF);
      $yL = chr($targetH & 0xFF);
      $yH = chr(($targetH >> 8) & 0xFF);

      $raster = "\x1Ba\x01";
      $raster .= "\x1D\x76\x30\x00" . $xL . $xH . $yL . $yH;

      for ($y = 0; $y < $targetH; $y++) {
        for ($bx = 0; $bx < $widthBytes; $bx++) {
          $byte = 0;
          for ($bit = 0; $bit < 8; $bit++) {
            $x = ($bx * 8) + $bit;
            if ($x >= $targetW) {
              continue;
            }
            $rgb = imagecolorat($resized, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            $gray = (int) (($r * 0.3) + ($g * 0.59) + ($b * 0.11));
            // 1 bit black. Threshold tuned for logos.
            if ($gray < 170) {
              $byte |= (1 << (7 - $bit));
            }
          }
          $raster .= chr($byte);
        }
      }

      imagedestroy($resized);
      return $raster;
    }

    public function printOriginal($saleId, Request $request)
    {
      $result = $this->printControlService->executeOriginal(
        $saleId,
        Auth::user(),
        $this->shopId(),
        $request->get('source', 'POS_AUTO'),
        $request->ip(),
        $request->userAgent()
      );

      if (!$result['allowed']) {
        return response()->json(['status' => 'ERROR', 'message' => $result['message']], $result['status']);
      }

      $previewToken = $this->buildPrintGrant($saleId, 'original', 'POS_AUTO', null, 0);
      $localToken = $this->buildPrintGrant($saleId, 'original', 'POS_AUTO', null, 0);

      $agentEnabled = $this->localPrintAgentService->isEnabled();
      $agentClientMode = $agentEnabled && $this->localPrintAgentService->isClientDispatch();
      $agentAttempted = $agentEnabled && !$agentClientMode;

      $agentResult = ['ok' => false, 'message' => 'not attempted'];
      $clientPrint = null;

      if ($agentEnabled) {
        try {
          $data = $this->buildVoucherData($saleId);
          $printMeta = [
            'is_reprint' => false,
            'copy_number' => 0,
            'reason' => null,
            'printed_by' => Auth::user()->name . ' ' . Auth::user()->last_name,
            'printed_at' => Date::now()->format('Y-m-d H:i:s'),
          ];

          if ($this->localPrintAgentService->mode() === 'pdf') {
            $binary = $this->buildVoucherPdfBinary($data, $printMeta);
            $filename = 'voucher-' . $saleId . '.pdf';
          } else {
            $binary = $this->buildEscPosPayload($data, $printMeta);
            $filename = 'voucher-' . $saleId . '.bin';
          }

          if ($agentClientMode) {
            $clientPrint = $this->localPrintAgentService->buildClientPayload($binary, $filename, 1);
          } else {
            if ($this->localPrintAgentService->mode() === 'pdf') {
              $agentResult = $this->localPrintAgentService->dispatchPdf($binary, $filename, 1);
            } else {
              $agentResult = $this->localPrintAgentService->dispatchRaw($binary, $filename, 1);
            }
          }
        } catch (\Exception $e) {
          $agentResult = ['ok' => false, 'message' => $e->getMessage()];
        }
      }

      return response()->json([
        'status' => 'OK',
        'preview_url' => '/generar-voucher-interno/' . $saleId . '?print_token=' . $previewToken . '&mode=original',
        'local_url' => '/generar-voucher-local/' . $saleId . '?print_token=' . $localToken . '&mode=original',
        'agent_attempted' => $agentAttempted,
        'agent_dispatched' => (bool) ($agentResult['ok'] ?? false),
        'agent_message' => $agentResult['message'] ?? null,
        'agent_job_id' => $agentResult['job_id'] ?? null,
        'client_print' => $clientPrint,
      ]);
    }

    public function reprintExecute($saleId, Request $request)
    {
      $reason = trim((string) $request->get('reason', ''));

      $result = $this->printControlService->executeReprint(
        $saleId,
        Auth::user(),
        $this->shopId(),
        $reason,
        Auth::id(),
        $request->get('source', 'ADMIN_PANEL'),
        $request->ip(),
        $request->userAgent()
      );

      if (!$result['allowed']) {
        return response()->json(['status' => 'ERROR', 'message' => $result['message']], $result['status']);
      }

      $previewToken = $this->buildPrintGrant($saleId, 'reprint', 'ADMIN_PANEL', $reason, $result['copy_number']);
      $localToken = $this->buildPrintGrant($saleId, 'reprint', 'ADMIN_PANEL', $reason, $result['copy_number']);

      $agentEnabled = $this->localPrintAgentService->isEnabled();
      $agentClientMode = $agentEnabled && $this->localPrintAgentService->isClientDispatch();
      $agentAttempted = $agentEnabled && !$agentClientMode;

      $agentResult = ['ok' => false, 'message' => 'not attempted'];
      $clientPrint = null;

      if ($agentEnabled) {
        try {
          $data = $this->buildVoucherData($saleId);
          $printMeta = [
            'is_reprint' => true,
            'copy_number' => (int) $result['copy_number'],
            'reason' => $reason,
            'printed_by' => Auth::user()->name . ' ' . Auth::user()->last_name,
            'printed_at' => Date::now()->format('Y-m-d H:i:s'),
          ];

          if ($this->localPrintAgentService->mode() === 'pdf') {
            $binary = $this->buildVoucherPdfBinary($data, $printMeta);
            $filename = 'voucher-reprint-' . $saleId . '.pdf';
          } else {
            $binary = $this->buildEscPosPayload($data, $printMeta);
            $filename = 'voucher-reprint-' . $saleId . '.bin';
          }

          if ($agentClientMode) {
            $clientPrint = $this->localPrintAgentService->buildClientPayload($binary, $filename, 1);
          } else {
            if ($this->localPrintAgentService->mode() === 'pdf') {
              $agentResult = $this->localPrintAgentService->dispatchPdf($binary, $filename, 1);
            } else {
              $agentResult = $this->localPrintAgentService->dispatchRaw($binary, $filename, 1);
            }
          }
        } catch (\Exception $e) {
          $agentResult = ['ok' => false, 'message' => $e->getMessage()];
        }
      }

      return response()->json([
        'status' => 'OK',
        'preview_url' => '/generar-voucher-interno/' . $saleId . '?print_token=' . $previewToken . '&mode=reprint',
        'local_url' => '/generar-voucher-local/' . $saleId . '?print_token=' . $localToken . '&mode=reprint',
        'agent_attempted' => $agentAttempted,
        'agent_dispatched' => (bool) ($agentResult['ok'] ?? false),
        'agent_message' => $agentResult['message'] ?? null,
        'agent_job_id' => $agentResult['job_id'] ?? null,
        'client_print' => $clientPrint,
      ]);
    }

    public function logs($saleId)
    {
      if ((int) Auth::user()->fk_id_user_type !== 1) {
        return response()->json(['status' => 'ERROR', 'message' => 'No autorizado'], 403);
      }

      $sale = $this->printControlService->validateSaleOwnership($saleId, $this->shopId());
      if (!$sale) {
        return response()->json(['status' => 'ERROR', 'message' => 'Venta no encontrada'], 404);
      }

      $logs = $this->printControlService->getSaleLogs($saleId, $this->shopId());
      return response()->json(['status' => 'OK', 'logs' => $logs]);
    }

    public function local($id, Request $request) {
      \Log::channel('single')->info('[VoucherLocal] Inicio', ['sale_id' => $id, 'mode' => $request->get('mode')]);
      try {
          $mode = $request->get('mode', 'original');
          $grant = $this->consumePrintGrant($request->get('print_token'), $id, $mode);

          if (!$grant) {
            \Log::channel('single')->warning('[VoucherLocal] Token invalido', ['sale_id' => $id]);
            return response()->json(['status' => 'ERROR', 'message' => 'Token de impresion invalido o expirado.'], 403);
          }

          $data = $this->buildVoucherData($id);
          $sales = $data['sales'];
          $sales_items = $data['sales_items'];
          $photo = $data['photo'];
          $config = $data['config'];
          $shop_data = $data['shop_data'];
          $printMeta = $this->printMetaFromGrant($grant);

          \Log::channel('single')->info('[VoucherLocal] Generando ESC/POS', ['sale_id' => $id, 'printer_name' => $config->printer_name ?? 'N/A']);
          ob_start();
          $connector = new FilePrintConnector('php://output');
          $printer = new Printer($connector);

          $items = [];
          foreach ($sales_items as $value) {
            array_push($items, ...array(new item($value->quantity . 'x ' . $value->name_item, '$ ' . number_format($value->price))));
          }
          $disc = round($sales[0]->discount * $sales[0]->amount / 100 / 10) * 10;
          $subtotal = new item('SUBTOTAL', '$ ' . number_format($sales[0]->amount));
          $tax = new item('DESCUENTO', number_format($sales[0]->discount) . '% $ ' . $disc);
          $total = new item('TOTAL', number_format($sales[0]->amount - $disc), true);

          if ($config->voucher_logo) {
            try {
              $printer->setJustification(Printer::JUSTIFY_CENTER);
              $tux = EscposImage::load($photo->photo, false);
              $printer->bitImageColumnFormat($tux);
            } catch (\Exception $logoEx) {
              \Log::channel('single')->warning('[VoucherLocal] Logo omitido', ['sale_id' => $id, 'error' => $logoEx->getMessage()]);
            }
          }

          $printer->setJustification(Printer::JUSTIFY_CENTER);
          $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
          $printer->text('OSAN POS\n');
          $printer->selectPrintMode();
          if ($sales[0]->folio) {
            $printer->text('N DOCUMENTO: ' . $sales[0]->folio . '\n');
          }

          $printer->text($shop_data[0]->name . ' ' . $shop_data[0]->last_name . '\n');
          $printer->text($shop_data[0]->address . '\n');
          $printer->text($shop_data[0]->phone . '\n');
          $printer->text($shop_data[0]->city);
          $printer->feed();

          $printer->setJustification(Printer::JUSTIFY_LEFT);
          $printer->setEmphasis(true);
          $printer->text(new item('', ''));
          $printer->setEmphasis(false);
          foreach ($items as $item) {
              $printer->text($item);
          }
          $printer->setEmphasis(true);
          $printer->text($subtotal);
          $printer->setEmphasis(false);
          $printer->feed();

          if ($sales[0]->discount != '0') {
            $printer->text($tax);
          }

          $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
          $printer->text($total);
          $printer->selectPrintMode();

          $printer->feed(1);
          $printer->setJustification(Printer::JUSTIFY_CENTER);
          if ($printMeta['is_reprint']) {
            $printer->text('*** REIMPRESION - COPIA ' . $printMeta['copy_number'] . ' ***\n');
            $printer->text('MOTIVO: ' . $printMeta['reason'] . '\n');
          }
          $printer->text('** GRACIAS POR SU PREFERENCIA **\n');
          $printer->text('CONTROL INTERNO\n');
          $printer->feed(1);
          $printer->text(ucfirst(Date::now()->format('l j F Y H:i:s') . "\n"));

          $printer->cut();
          $printer->close();

          $escposBytes = ob_get_clean();
          \Log::channel('single')->info('[VoucherLocal] ESC/POS generado', ['sale_id' => $id, 'bytes' => strlen($escposBytes)]);

          /** @var LocalPrintAgentService $printAgent */
          $printAgent = app(LocalPrintAgentService::class);

          \Log::channel('single')->info('[VoucherLocal] Preparando envio al agente', [
              'sale_id' => $id,
              'enabled' => $printAgent->isEnabled(),
              'mode' => $printAgent->mode(),
              'dispatch' => $printAgent->dispatchMode(),
          ]);

          // Cuando dispatch=client, el navegador es quien hace el POST al agente.
          // Devolvemos el payload base64 y dejamos que el front lo despache.
          if ($printAgent->isClientDispatch()) {
              $clientPrint = $printAgent->buildClientPayload($escposBytes, 'voucher-' . $id . '.bin', 1);
              if ($clientPrint === null) {
                  return response()->json(['status' => 'ERROR', 'message' => 'Agente de impresion deshabilitado.'], 500);
              }
              return response()->json([
                  'printer' => 'CLIENT_DISPATCH',
                  'client_print' => $clientPrint,
              ]);
          }

          $result = $printAgent->dispatchRaw($escposBytes, 'voucher-' . $id . '.bin');

          \Log::channel('single')->info('[VoucherLocal] Respuesta agente', ['sale_id' => $id, 'result' => $result]);

          if (!$result['ok']) {
              return response()->json(['status' => 'ERROR', 'message' => $result['message'] ?? 'No se pudo enviar al agente de impresion.'], 500);
          }

          return response()->json(['printer' => 'OK']);
      } catch (\Exception $e) {
          \Log::channel('single')->error('[VoucherLocal] Exception', [
              'sale_id' => $id,
              'error' => $e->getMessage(),
              'file' => $e->getFile(),
              'line' => $e->getLine(),
              'trace' => $e->getTraceAsString(),
          ]);
          return response()->json(['status' => 'ERROR', 'message' => $e->getMessage()], 500);
      }
    }

    public function remota($id, Request $request) {
        try {
            $mode = $request->get('mode', 'original');
            $grant = $this->consumePrintGrant($request->get('print_token'), $id, $mode);
            if (!$grant) {
              $pdf = \PDF::loadHTML('<h2>Token de impresion invalido o expirado.</h2>');
              return $pdf->stream();
            }

            $data = $this->buildVoucherData($id);
            $printMeta = $this->printMetaFromGrant($grant);
            return $this->streamVoucherPdf($data, $printMeta);
        } catch (\Exception $e) {
            $pdf = \PDF::loadHTML('<h2>No se encontro la venta asociada, por favor intente nuevamente o comuniquese con un administrador.</h2>');
            return $pdf->stream();
        }
    }
}