<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
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

    public function __construct() {  
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

      private function printBlockedResponse($sale, $jsonResponse)
      {
        if ($jsonResponse) {
          return response()->json([
            'status' => 'blocked',
            'message' => 'Este voucher ya fue impreso y no se permite reimpresion.',
            'sale_id' => $sale ? $sale->id : null,
            'first_printed_at' => $sale ? $sale->voucher_first_printed_at : null,
          ], 409);
        }

        return response()->view('invoice.voucher_blocked', [
          'sale' => $sale,
          'autoPrint' => request()->boolean('auto_print'),
        ], 409);
      }

      private function trackVoucherPrint($id, $jsonResponse = true)
      {
        $sale = DB::table('sales')->where('id', $id)->first();

        if (!$sale) {
          if ($jsonResponse) {
            return [
              'sale' => null,
              'response' => response()->json([
                'status' => 'error',
                'message' => 'No se encontro la venta asociada.',
              ], 404),
            ];
          }

          return [
            'sale' => null,
            'response' => response()->view('invoice.voucher_blocked', [
              'sale' => null,
              'autoPrint' => request()->boolean('auto_print'),
            ], 404),
          ];
        }

        $isAdmin = Auth::user()->fk_id_user_type == 1;
        $allowReprint = $isAdmin && request()->boolean('reprint');
        $alreadyPrinted = (int) ($sale->voucher_print_count ?? 0) > 0;

        if ($alreadyPrinted && !$allowReprint) {
          return [
            'sale' => $sale,
            'response' => $this->printBlockedResponse($sale, $jsonResponse),
          ];
        }

        $authId = (int) Auth::id();

        DB::table('sales')->where('id', $id)->update([
          'voucher_print_count' => DB::raw('COALESCE(voucher_print_count, 0) + 1'),
          'voucher_first_printed_at' => DB::raw('COALESCE(voucher_first_printed_at, NOW())'),
          'voucher_first_printed_by' => DB::raw('COALESCE(voucher_first_printed_by, ' . $authId . ')'),
          'voucher_last_printed_at' => now(),
          'voucher_last_printed_by' => $authId,
        ]);

        return [
          'sale' => DB::table('sales')->where('id', $id)->first(),
          'response' => null,
        ];
      }

      public function local(Request $request, $id) {
          $printState = $this->trackVoucherPrint($id, true);
          if ($printState['response']) {
          return $printState['response'];
          }

          $sales = [$printState['sale']];
          $sales_items = DB::table("sales_item")->where("fk_sales_id", $sales[0]->id)->get()->toArray();
          $photo = DB::table('users')->select('photo')->where('id', Auth::id())->first();
          $config = DB::table('page_config')->where('fk_user_id', $this->userID)->first();
          $shop_data = DB::table('users')->where('id', $this->userID)
          ->select('name', 'last_name', 'phone', 'city', 'address')
          ->get()->toArray();

          $shop_name_eccomerce = DB::table('page_config')->select('name_ecommerce')->where('fk_user_id', $this->userID)->first();

          $connector = new WindowsPrintConnector($config->printer_name);
          $printer = new Printer($connector);
          
          $items = [];
          foreach ($sales_items as $value) {
            array_push($items, ...array(new item($value->quantity."x ".$value->name_item, "$ ".number_format($value->price))));
          }
          $disc = round($sales[0]->discount * $sales[0]->amount / 100 / 10) * 10;
          $subtotal = new item('SUBTOTAL', "$ ".number_format($sales[0]->amount));
          $tax = new item('DESCUENTO', number_format($sales[0]->discount)."% $ ".$disc);
          $total = new item('TOTAL', number_format($sales[0]->amount - $disc), true);

          if($config->voucher_logo) {
          /* Start the printer */
          $printer->setJustification(Printer::JUSTIFY_CENTER);
          $tux = EscposImage::load($photo->photo, false);
          $printer -> bitImageColumnFormat($tux);
          }
          
          /* Name of shop */
          $printer -> setJustification(Printer::JUSTIFY_CENTER);
          $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
          $printer -> text("OSAN POS\n");
          $printer -> selectPrintMode();          
          if($sales[0]->folio) {
            $printer -> text("N? DOCUMENTO: 42\n");
          }
          
          $printer -> text($shop_data[0]->name." ".$shop_data[0]->last_name."\n");
          $printer -> text($shop_data[0]->address."\n");
          $printer -> text($shop_data[0]->phone."\n");
          $printer -> text($shop_data[0]->city);             
          $printer -> feed();         

          /* Items */
          $printer -> setJustification(Printer::JUSTIFY_LEFT);
          $printer -> setEmphasis(true);
          $printer -> text(new item('', ''));
          $printer -> setEmphasis(false);
          foreach ($items as $item) {
              $printer -> text($item);
          }
          $printer -> setEmphasis(true);
          $printer -> text($subtotal);
          $printer -> setEmphasis(false);
          $printer -> feed();

           /* Tax and total */
           if($sales[0]->discount != "0"){
            $printer -> text($tax);
           }
           
          $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
          $printer -> text($total);
          $printer -> selectPrintMode();

          /* Footer */
          $printer -> feed(1);
          $printer -> setJustification(Printer::JUSTIFY_CENTER);
          $printer -> text("** GRACIAS POR SU PREFERENCIA **\n");
          $printer -> text("CONTROL INTERNO\n");
          $printer -> feed(1);
          $printer -> text(ucfirst(Date::now()->format('l j F Y H:i:s'). "\n"));

          $printer -> cut();
          //$printer -> pulse(); ABRIR GABETA DE DINERO

          $printer -> close();
          return response()->json(["printer" => "OK"]);
       
    }

    public function remota(Request $request, $id) {
        try {            
            $printState = $this->trackVoucherPrint($id, false);
            if ($printState['response']) {
              return $printState['response'];
            }

            $sales = [$printState['sale']];
            $sales_items = DB::table("sales_item")->where("fk_sales_id", $sales[0]->id)->get()->toArray();
            $photo = DB::table('users')->select('photo')->where('id', $this->userID)->first();
            $config = DB::table('page_config')->where('fk_user_id', $this->userID)->first();
            $shop_data = DB::table('users')->where('id', $this->userID)
            ->select('name', 'last_name', 'phone', 'city', 'address')
            ->get()->toArray();

            $shop_name_eccomerce = DB::table('page_config')->select('name_ecommerce')->where('fk_user_id', $this->userID)->first();

            if ($request->boolean('print_mode')) {
              $view = $config->continuous_paper_type === 58 ? 'invoice.voucher_58' : 'invoice.voucher_80';
              return view($view, [
                'sales' => $sales,
                'sales_items' => $sales_items,
                'shop_data' => $shop_data,
                'shop_name_eccomerce' => $shop_name_eccomerce,
                'photo' => $photo,
                'config' => $config,
                'auto_print' => $request->boolean('auto_print'),
              ]);
            }

            if($config->voucher_logo) {
              $pdfWidth  = 250 + count($sales_items) * 70;
            } else {
              $pdfWidth  = 160 + count($sales_items) * 70;
            }

            if($config->continuous_paper_type === 58) {
              $pdfHeight = 140; // 58mm
              $pdf = \PDF::loadView('invoice.voucher_58', compact("sales", "sales_items", "shop_data", "shop_name_eccomerce", "photo", "config"))->setPaper(array(0,0,$pdfWidth,$pdfHeight), 'landscape');
              return $pdf->stream('archivo.pdf');
            } else {
              $pdfHeight = 210; // 80mm
              $pdf = \PDF::loadView('invoice.voucher_80', compact("sales", "sales_items", "shop_data", "shop_name_eccomerce", "photo", "config"))->setPaper(array(0,0,$pdfWidth,$pdfHeight), 'landscape');
              return $pdf->stream('archivo.pdf');
            }

        } catch (\Exception $e) {
            $pdf = \PDF::loadHTML('<h2>No se encontro la venta asociada, por favor intente nuevamente o comuniquese con un administrador.</h2>');
            return $pdf->stream();
        }
    }
}