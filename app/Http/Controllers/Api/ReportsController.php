<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class ReportsController extends Controller
{
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
}
