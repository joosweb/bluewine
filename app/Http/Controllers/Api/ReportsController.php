<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LocalPrintAgentService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;

class ReportsController extends Controller
{
    private $userID;
    private $isAdmin;
    private $localPrintAgentService;

    public function __construct(LocalPrintAgentService $localPrintAgentService)
    {
        $this->localPrintAgentService = $localPrintAgentService;
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->fk_id_user_type == 1) {
                $this->isAdmin = true;
                $this->userID = Auth::id(); // IS ADMIN
            } else {
                $query = DB::table('shop')
                    ->select('fk_user_id_admin')
                    ->where('id', Auth::user()->shop)
                    ->first();
                $this->isAdmin = false;
                $this->userID = $query->fk_user_id_admin;
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $day = $request->input('day');
        $seller_id = $request->input('seller_id');

        $sales = DB::table('sales')
            ->join('sales_item', 'sales.id', '=', 'sales_item.fk_sales_id')
            ->join('users', 'sales.fk_user_id', '=', 'users.id')
            ->select(
                'users.name as seller_name',
                'sales_item.code',
                'sales_item.quantity',
                'sales_item.price',
                'sales_item.name_item',
                'sales_item.fk_sales_id'
            )
            ->where('sales.created_at', 'like', '%' . $day . '%');

        if ($seller_id) {
            $sales->where('sales.fk_user_id', $seller_id);
        }


        $groupedSales = [];

        // Iteramos sobre los resultados y agrupamos por vendedor y código de producto
        foreach ($sales->get() as $sale) {
            $sellerName = $sale->seller_name;
            $code = $sale->code;
            $price = $sale->price;
            $subtotal = $sale->quantity * $sale->price;

            // Si el vendedor ya existe en el array agrupado, verificamos si el producto ya existe
            if (array_key_exists($sellerName, $groupedSales)) {
                // Si el producto ya existe para este vendedor, actualizamos las cantidades y el subtotal
                if (
                    array_key_exists(
                        $code,
                        $groupedSales[$sellerName]['products']
                    )
                ) {
                    $groupedSales[$sellerName]['products'][$code]['quantity'] +=
                        $sale->quantity;
                    $groupedSales[$sellerName]['products'][$code][
                        'subtotal'
                    ] += $subtotal;
                } else {
                    // Si es la primera vez que encontramos este producto para este vendedor, lo agregamos al array agrupado
                    $groupedSales[$sellerName]['products'][$code] = [
                        'name_item' => $sale->name_item,
                        'quantity' => $sale->quantity,
                        'price' => $price,
                        'subtotal' => $subtotal,
                    ];
                }

                // Actualizamos el total general del vendedor
                $groupedSales[$sellerName]['total'] += $subtotal;
            } else {
                // Si es la primera vez que encontramos este vendedor, creamos una entrada para él en el array agrupado
                $groupedSales[$sellerName] = [
                    'products' => [
                        $code => [
                            'name_item' => $sale->name_item,
                            'quantity' => $sale->quantity,
                            'price' => $price,
                            'subtotal' => $subtotal,
                        ],
                    ],
                    'total' => $subtotal, // Inicializamos el total general del vendedor
                ];
            }
        }

        return response()->json($groupedSales, 200);
    }

    /**
     * Reporte de ventas por producto.
     *
     * Devuelve la lista de productos vendidos en un rango de fechas
     * (o en el día actual si no se especifica), agrupados por código de producto,
     * mostrando cantidad vendida, precio y subtotal. Además entrega totales
     * generales útiles para cierre de caja.
     */
    public function products(Request $request)
    {
        $data = $this->buildProductsReportData($request);

        return response()->json($data, 200);
    }

    /**
     * Devuelve el mismo reporte de productos, pero como bytes ESC/POS listos para
     * que el navegador los envíe al agente local (modo dispatch=client).
     */
    public function productsPrint(Request $request)
    {
        if (!$this->localPrintAgentService->isEnabled()) {
            return response()->json([
                'status'  => 'ERROR',
                'message' => 'El agente de impresion local esta deshabilitado.',
            ], 422);
        }

        $data = $this->buildProductsReportData($request);

        if (empty($data['products']) || count($data['products']) === 0) {
            return response()->json([
                'status'  => 'ERROR',
                'message' => 'No hay datos para imprimir en el rango seleccionado.',
            ], 422);
        }

        $paperType = (int) DB::table('page_config')
            ->where('fk_user_id', $this->userID)
            ->value('continuous_paper_type');
        if ($paperType !== 80) {
            $paperType = 58;
        }

        $shopName = (string) DB::table('page_config')
            ->where('fk_user_id', $this->userID)
            ->value('name_ecommerce');
        if ($shopName === '') {
            $shopName = 'BLUEWINE';
        }

        $escposBytes = $this->buildProductsReportEscPos($data, $shopName, $paperType);

        $clientPrint = $this->localPrintAgentService->buildClientPayload(
            $escposBytes,
            'reporte-productos.bin',
            1
        );

        return response()->json([
            'status'       => 'OK',
            'client_print' => $clientPrint,
            // Eco de los totales para que el front pueda mostrar feedback si quiere.
            'summary'      => [
                'from'           => $data['from'],
                'to'             => $data['to'],
                'total_quantity' => $data['total_quantity'],
                'total_amount'   => $data['total_amount'],
                'total_lines'    => $data['total_lines'],
                'sales_count'    => $data['sales_count'],
            ],
        ], 200);
    }

    /**
     * Centraliza la consulta del reporte de productos para que la respondan tanto
     * `products()` (JSON para la tabla) como `productsPrint()` (ESC/POS).
     */
    private function buildProductsReportData(Request $request)
    {
        $from      = $request->input('from');
        $to        = $request->input('to');
        $sellerId  = $request->input('seller_id');
        $search    = $request->input('search');

        if (!$from && !$to) {
            $from = Carbon::now()->startOfDay()->toDateTimeString();
            $to   = Carbon::now()->endOfDay()->toDateTimeString();
        } else {
            try {
                $from = Carbon::parse($from)->startOfDay()->toDateTimeString();
            } catch (\Exception $e) {
                $from = Carbon::now()->startOfDay()->toDateTimeString();
            }
            try {
                $to = Carbon::parse($to)->endOfDay()->toDateTimeString();
            } catch (\Exception $e) {
                $to = Carbon::now()->endOfDay()->toDateTimeString();
            }
        }

        $query = DB::table('sales as T1')
            ->join('sales_item as T2', 'T1.id', '=', 'T2.fk_sales_id')
            ->select(
                'T2.code',
                'T2.name_item',
                DB::raw('SUM(T2.quantity) as total_quantity'),
                DB::raw('AVG(T2.price) as avg_price'),
                DB::raw('SUM(T2.quantity * T2.price) as subtotal'),
                DB::raw('COUNT(DISTINCT T1.id) as sales_count')
            )
            ->whereBetween('T1.created_at', [$from, $to])
            ->whereIn('T1.type_document', [0, 1, 2]);

        if ($this->isAdmin) {
            $query->where('T1.fk_shop_id', Auth::user()->shop);
        } else {
            $query->where('T1.fk_user_id', Auth::id());
        }

        if ($sellerId) {
            $query->where('T1.fk_user_id', $sellerId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('T2.code', 'LIKE', '%' . $search . '%')
                  ->orWhere('T2.name_item', 'LIKE', '%' . $search . '%');
            });
        }

        $query->groupBy('T2.code', 'T2.name_item')
              ->orderBy('subtotal', 'DESC');

        $products = $query->get();

        $total_quantity = 0;
        $total_amount   = 0;
        $total_lines    = 0;

        foreach ($products as $p) {
            $total_quantity += (int) $p->total_quantity;
            $total_amount   += (float) $p->subtotal;
            $total_lines    += 1;
        }

        $salesCountQuery = DB::table('sales as T1')
            ->whereBetween('T1.created_at', [$from, $to])
            ->whereIn('T1.type_document', [0, 1, 2]);

        if ($this->isAdmin) {
            $salesCountQuery->where('T1.fk_shop_id', Auth::user()->shop);
        } else {
            $salesCountQuery->where('T1.fk_user_id', Auth::id());
        }
        if ($sellerId) {
            $salesCountQuery->where('T1.fk_user_id', $sellerId);
        }

        return [
            'from'           => $from,
            'to'             => $to,
            'products'       => $products,
            'total_quantity' => $total_quantity,
            'total_amount'   => $total_amount,
            'total_lines'    => $total_lines,
            'sales_count'    => $salesCountQuery->count(),
        ];
    }

    /**
     * Genera el ticket ESC/POS del reporte de productos. Mismo estilo que el
     * voucher de ventas: ASCII puro, comandos básicos para máxima compatibilidad.
     */
    private function buildProductsReportEscPos(array $data, $shopName, $paperType)
    {
        $is58 = ($paperType === 58);
        $lineWidth   = $is58 ? 32 : 42;
        $qtyWidth    = $is58 ? 4  : 6;
        $priceWidth  = $is58 ? 10 : 12;
        $codeWidth   = $is58 ? 8  : 10;
        $nameWidth   = $lineWidth - $qtyWidth - $priceWidth;

        $text  = '';
        $text .= "\x1B@";       // init
        $text .= "\x1BM\x00";   // font A
        $text .= "\x1Ba\x01";   // align center

        // Cabecera
        $text .= $this->toAscii(strtoupper($shopName)) . "\n";
        $text .= "REPORTE DE PRODUCTOS VENDIDOS\n";

        $text .= "\x1Ba\x00";   // align left
        $text .= str_repeat('-', $lineWidth) . "\n";
        $text .= 'DESDE: ' . Carbon::parse($data['from'])->format('d/m/Y H:i') . "\n";
        $text .= 'HASTA: ' . Carbon::parse($data['to'])->format('d/m/Y H:i') . "\n";
        $text .= str_repeat('-', $lineWidth) . "\n";

        // Encabezado de columnas
        $text .= $this->padRight('CANT', $qtyWidth)
               . $this->padRight('PRODUCTO', $nameWidth)
               . $this->padLeft('SUBTOTAL', $priceWidth) . "\n";
        $text .= str_repeat('-', $lineWidth) . "\n";

        // Detalle producto a producto
        foreach ($data['products'] as $p) {
            $name = $this->toAscii(strtoupper((string) $p->name_item));
            $code = trim((string) ($p->code ?? ''));

            $line = $this->padRight((int) $p->total_quantity . 'x', $qtyWidth)
                  . $this->padRight($this->truncate($name, $nameWidth), $nameWidth)
                  . $this->padLeft('$' . number_format((int) round($p->subtotal)), $priceWidth);
            $text .= $line . "\n";

            // Si hay código, lo mostramos en una segunda línea más compacta
            if ($code !== '') {
                $detail = '  COD: ' . $this->truncate($code, $codeWidth)
                        . '  PROM: $' . number_format((int) round($p->avg_price));
                $text .= $this->truncate($detail, $lineWidth) . "\n";
            }
        }

        $text .= str_repeat('-', $lineWidth) . "\n";

        // Totales
        $text .= $this->padRight('VENTAS', $lineWidth - $priceWidth)
               . $this->padLeft(number_format((int) $data['sales_count']), $priceWidth) . "\n";
        $text .= $this->padRight('PRODUCTOS', $lineWidth - $priceWidth)
               . $this->padLeft(number_format((int) $data['total_lines']), $priceWidth) . "\n";
        $text .= $this->padRight('UNIDADES', $lineWidth - $priceWidth)
               . $this->padLeft(number_format((int) $data['total_quantity']), $priceWidth) . "\n";
        $text .= $this->padRight('TOTAL', $lineWidth - $priceWidth)
               . $this->padLeft('$' . number_format((int) round($data['total_amount'])), $priceWidth) . "\n";
        $text .= str_repeat('-', $lineWidth) . "\n";

        $text .= "\x1Ba\x01";
        $text .= 'IMPRESO ' . Carbon::now()->format('d/m/Y H:i') . "\n";

        // Feed + cut
        $text .= "\x1Bd\x05";
        $text .= "\x1DV\x00";

        return $text;
    }

    private function toAscii($text)
    {
        $clean = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', (string) $text);
        if ($clean === false) {
            $clean = (string) $text;
        }
        return $clean;
    }

    private function padRight($text, $width)
    {
        $text = (string) $text;
        $len = strlen($text);
        if ($len >= $width) {
            return substr($text, 0, $width);
        }
        return $text . str_repeat(' ', $width - $len);
    }

    private function padLeft($text, $width)
    {
        $text = (string) $text;
        $len = strlen($text);
        if ($len >= $width) {
            return substr($text, $len - $width, $width);
        }
        return str_repeat(' ', $width - $len) . $text;
    }

    private function truncate($text, $width)
    {
        $text = (string) $text;
        if (strlen($text) <= $width) {
            return $text;
        }
        return substr($text, 0, $width);
    }
}
