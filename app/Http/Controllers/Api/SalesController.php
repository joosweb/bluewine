<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Auth;

class SalesController extends Controller
{
  private $userID;

  public function __construct(){
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

  public function index() {
      if(Auth::user()->fk_id_user_type == 2) {
        $query = DB::table('sales as T1')
        ->select('T1.fk_cliente_id','T3.name as vname','T3.last_name as vlast_name','T1.type_document','T1.urlpdf','T1.folio','T1.informedSii','T1.created_at', 'T1.id','T1.discount_type','T1.discount', 'T1.amount','T2.run', 'T2.name', 'T2.last_name')
        ->leftJoin('clients as T2', 'T1.fk_cliente_id', '=', 'T2.id')
        ->leftJoin('users as T3', 'T1.fk_user_id', '=', 'T3.id')
        ->where('T1.fk_user_id', Auth::id())
        ->orderBy('T1.created_at', 'DESC')
        ->paginate(8);
        return $query;
      }
      else {
        $query = DB::table('sales as T1')
        ->select('T1.fk_cliente_id','T1.fk_user_id','T3.name as vname','T3.last_name as vlast_name','T1.type_document','T1.urlpdf','T1.folio','T1.informedSii','T1.created_at', 'T1.id','T1.discount_type','T1.discount', 'T1.amount','T2.run', 'T2.name', 'T2.last_name')
        ->leftJoin('clients as T2', 'T1.fk_cliente_id', '=', 'T2.id')
        ->leftJoin('users as T3', 'T1.fk_user_id', '=', 'T3.id')
        ->where('T1.fk_shop_id', Auth::user()->shop)
        ->orderBy('T1.created_at', 'DESC')
        ->paginate(8);
        return $query;
      }
  }

  public function carts_processed($filter,$value = false,$from,$to) {
    $query = DB::table('sales as T1');
    if($value){
      $query->where($filter, 'LIKE', '%'.$value.'%');
    }
    if($from && $to) {
      $query->whereBetween('T1.created_at', [$from, $to]);
    }
    if(Auth::user()->fk_id_user_type == 1) {
      $query->where('T1.fk_shop_id', Auth::user()->shop);
    }
    else {
      $query->where('T1.fk_user_id', Auth::id());
    }
    return $query->count();
  }

  public function total_status_informesii($filter,$value = false,$from,$to){

    $success=0;
    $rejected=0;
    $sent=0;

    $query = DB::table('sales as T1')
    ->select('T1.informedSii', DB::raw('COUNT(informedSii) AS suma'));
    if($value){
      $query->where($filter, 'LIKE', '%'.$value.'%');
    }
    if($from && $to) {
      $query->whereBetween('T1.created_at', [$from, $to]);
    }
    if(Auth::user()->fk_id_user_type == 1) {
      $query->where('T1.fk_shop_id', Auth::user()->shop);
    }
    else {
      $query->where('T1.fk_user_id', Auth::id());
    }
    $query->groupBy('T1.informedSii');

    $result = $query->get();

    foreach ($result as $key => $value) {
        if($value->informedSii == 0){
          $success = $value->suma;
        }
        else if($value->informedSii == 1) {
          $sent = $value->suma;
        }
        else if($value->informedSii == 2) {
          $rejected = $value->suma;
        }
     }
     $array = ['carritos_procesados' => $this->carts_processed($filter,$value = false,$from,$to), 'correctos' => $success, 'enviados' => $sent, 'rechazados' => $rejected];
     return $array;
  }

  public function ChartRange($filter,$value = false,$from,$to) {
    if(Auth::user()->fk_id_user_type == 1) {
      $query = DB::table('sales')
      ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) AS amount'))
      ->whereBetween('created_at', [$from, $to])
      ->where('fk_shop_id', Auth::user()->shop)
      ->whereIn('type_document', [0, 1, 2])
      ->groupBy('date')
      ->get();
      return $query;
    }
    else {
      $query = DB::table('sales')
      ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) AS amount'))
      ->whereBetween('created_at', [$from, $to])
      ->where('fk_user_id', Auth::id())
      ->groupBy('date')
      ->get();
      return $query;
    }
  }

  public function total_expenses_from_range($filter,$value = false,$from,$to){
    try {
      $query = DB::table('expenses')
      ->select(DB::raw('SUM(amount) AS amount'));
      if($value){
        $query->where($filter, 'LIKE', '%'.$value.'%');
      }
      if($from && $to) {
        $query->whereBetween('created_at', [$from, $to]);
      }
      if(Auth::user()->fk_id_user_type == 1) {
        $query->where('fk_user_shop', Auth::user()->shop);
      }
      else {
        $query->where('fk_user_id', Auth::id());
      }
      return $query->first()->amount;
    } catch (\Exception $e) {
      return 0;
    }
  }

  public function total_amount_from_range($filter,$value = false,$from,$to){
    $query = DB::table('sales as T1')
    ->select('T1.fk_cliente_id','T3.name as vname','T3.last_name as vlast_name','T1.type_document','T1.urlpdf','T1.folio','T1.informedSii','T1.created_at', 'T1.id','T1.discount_type','T1.discount', 'T1.amount','T2.run', 'T2.name', 'T2.last_name')
    ->leftJoin('clients as T2', 'T1.fk_cliente_id', '=', 'T2.id')
    ->leftJoin('users as T3', 'T1.fk_user_id', '=', 'T3.id')
    ->whereIn('T1.type_document', [0, 1, 2]);
    if($value){
      $query->where($filter, 'LIKE', '%'.$value.'%');
    }
    if($from && $to) {
      $query->whereBetween('T1.created_at', [$from, $to]);
    }
    if(Auth::user()->fk_id_user_type == 1) {
      $query->where('T1.fk_shop_id', Auth::user()->shop);
    }
    else {
      $query->where('T1.fk_user_id', Auth::id());
    }
    return number_format($query->sum('T1.amount'));
  }

  public function top_sellers($filter,$value = false,$from,$to){
    $query = DB::table('sales as T1')
    ->select('T3.name', 'T3.last_name','T1.fk_user_id', DB::raw('SUM(amount) AS amount'))
    ->leftJoin('users as T3', 'T1.fk_user_id', '=', 'T3.id')
    ->whereIn('T1.type_document', [0, 1, 2]);
    if($value){
      $query->where($filter, 'LIKE', '%'.$value.'%');
    }
    if($from && $to) {
      $query->whereBetween('T1.created_at', [$from, $to]);
    }
    if(Auth::user()->fk_id_user_type == 1) {
      $query->where('T1.fk_shop_id', Auth::user()->shop);
    }
    else {
      $query->where('T1.fk_user_id', Auth::id());
    }
    $query->groupBy('T3.name');
    return $query->get();
  }

  public function sales_for_type_payment($filter,$value = false,$from,$to){
    $query = DB::table('sales as T1')
    ->select('payment_method', DB::raw('SUM(amount) AS amount'))
    ->whereIn('type_document', [0, 1, 2]);
    if($value){
      $query->where($filter, 'LIKE', '%'.$value.'%');
    }
    if($from && $to) {
      $query->whereBetween('T1.created_at', [$from, $to]);
    }
    if(Auth::user()->fk_id_user_type == 1) {
      $query->where('T1.fk_shop_id', Auth::user()->shop);
    }
    else {
      $query->where('T1.fk_user_id', Auth::id());
    }
    $query->groupBy('payment_method');
    return $query->get();
  }

  public function sales_for_type_document($filter,$value = false,$from,$to){
    $query = DB::table('sales as T1')
    ->select('T1.type_document', DB::raw('COUNT(type_document) AS suma'));
    if($value){
      $query->where($filter, 'LIKE', '%'.$value.'%');
    }
    if($from && $to) {
      $query->whereBetween('T1.created_at', [$from, $to]);
    }
    if(Auth::user()->fk_id_user_type == 1) {
      $query->where('T1.fk_shop_id', Auth::user()->shop);
    }
    else {
      $query->where('T1.fk_user_id', Auth::id());
    }
    $query->groupBy('T1.type_document');

    $data = $query->get();

    $boletas=0;
    $facturas=0;
    $cotizaciones=0;
    $procesados=0;

    foreach ($data as $key => $value) {
      if($value->type_document == 0) {
          $procesados = $value->suma;
      }
      if($value->type_document == 1) {
          $boletas = $value->suma;
      }
      if($value->type_document == 2) {
          $facturas = $value->suma;
      }
      if($value->type_document == 3) {
          $cotizaciones = $value->suma;
      }
    }
    $array = [
      'boletas' => $boletas,
      'facturas' => $facturas,
      'cotizaciones' => $cotizaciones,
      'procesados' => $procesados,
    ];
    return $array;
  }

  public function ChartExpenses($from, $to) {
    if(Auth::user()->fk_id_user_type == 1) {
      $query = DB::table('expenses')
      ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) AS amount'))
      ->whereBetween('created_at', [$from, $to])
      ->where('fk_user_shop', Auth::user()->shop)
      ->groupBy('date')
      ->get();
      return $query;
    }
    else {
      $query = DB::table('expenses')
      ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) AS amount'))
      ->whereBetween('created_at', [$from, $to])
      ->where('fk_user_id', Auth::id())
      ->groupBy('date')
      ->get();
      return $query;
    }
  }

  public function show(Request $request) {
        //sleep(1);
        $query = DB::table('sales as T1')
        ->select('T1.fk_cliente_id','T1.fk_user_id','T3.name as vname','T3.last_name as vlast_name','T1.type_document','T1.urlpdf','T1.folio','T1.informedSii','T1.created_at', 'T1.id','T1.discount_type','T1.discount', 'T1.amount','T2.run', 'T2.name', 'T2.last_name')
        ->leftJoin('clients as T2', 'T1.fk_cliente_id', '=', 'T2.id')
        ->leftJoin('users as T3', 'T1.fk_user_id', '=', 'T3.id');
        if($request['value']){
          $query->where($request['filter'], 'LIKE', '%'.$request['value'].'%');
        }
        if($request['from'] && $request['to']) {
          $query->whereBetween('T1.created_at', [$request['from'], $request['to']]);
        }
        if(Auth::user()->fk_id_user_type == 1) {
          $query->where('T1.fk_shop_id', Auth::user()->shop);
        }
        else {
          $query->where('T1.fk_user_id', Auth::id());
        }
        $query->orderBy('T1.created_at', 'DESC');
        return response()->json(
          [
            'table' => @$query->paginate(8),
            'marcators' => @$this->total_status_informesii($request['filter'],$request['value'],$request['from'],$request['to']),
            'grafic_sales' => @$this->ChartRange($request['filter'],$request['value'],$request['from'],$request['to']),
            'grafic_expenses' => @$this->ChartExpenses($request['from'], $request['to']),
            'amount' => @$this->total_amount_from_range($request['filter'],$request['value'],$request['from'],$request['to']),
            'total_expenses_from_range' => @$this->total_expenses_from_range($request['filter'],$request['value'],$request['from'],$request['to']),
            'top_sellers' => @$this->top_sellers($request['filter'],$request['value'],$request['from'],$request['to']),
            'sales_for_type_payment' => @$this->sales_for_type_payment($request['filter'],$request['value'],$request['from'],$request['to']),
            'sales_for_type_document' => @$this->sales_for_type_document($request['filter'],$request['value'],$request['from'],$request['to']),
          ]
      );
  }

  public function printSummary(Request $request)
  {
    $query = DB::table('sales as T1');

    $filter = $request->get('filter');
    $value = $request->get('value');
    $from = $request->get('from');
    $to = $request->get('to');

    if ($value && $filter) {
      $query->where($filter, 'LIKE', '%' . $value . '%');
    }

    if ($from && $to) {
      $query->whereBetween('T1.created_at', [$from, $to]);
    }

    if (Auth::user()->fk_id_user_type == 1) {
      $query->where('T1.fk_shop_id', Auth::user()->shop);
    } else {
      $query->where('T1.fk_user_id', Auth::id());
    }

    $totalOriginalPrints = (int) (clone $query)->sum('T1.print_original_count');
    $totalReprints = (int) (clone $query)->sum('T1.reprint_count');
    $salesWithOriginal = (int) (clone $query)->where('T1.print_original_count', '>', 0)->count();
    $salesWithReprint = (int) (clone $query)->where('T1.reprint_count', '>', 0)->count();

    return response()->json([
      'total_original_prints' => $totalOriginalPrints,
      'total_reprints' => $totalReprints,
      'total_prints' => $totalOriginalPrints + $totalReprints,
      'sales_with_original' => $salesWithOriginal,
      'sales_with_reprint' => $salesWithReprint,
    ]);
  }

  public function reprintDashboard(Request $request)
  {
    $filter = $request->get('filter');
    $value = $request->get('value');
    $from = $request->get('from');
    $to = $request->get('to');

    $baseQuery = DB::table('voucher_print_logs as vpl')
      ->join('sales as s', 'vpl.sale_id', '=', 's.id')
      ->leftJoin('users as u', 'vpl.user_id', '=', 'u.id')
      ->where('vpl.action', 'REPRINT');

    if ($value && $filter) {
      if ($filter === 'T1.folio') {
        $baseQuery->where('s.folio', 'LIKE', '%' . $value . '%');
      }
    }

    if ($from && $to) {
      $baseQuery->whereBetween('s.created_at', [$from, $to]);
    }

    if (Auth::user()->fk_id_user_type == 1) {
      $baseQuery->where('s.fk_shop_id', Auth::user()->shop);
    } else {
      $baseQuery->where('s.fk_user_id', Auth::id());
    }

    $topReprinters = (clone $baseQuery)
      ->select(
        'vpl.user_id',
        DB::raw("CONCAT(COALESCE(u.name, ''), ' ', COALESCE(u.last_name, '')) as user_name"),
        DB::raw('COUNT(vpl.id) as reprint_count')
      )
      ->groupBy('vpl.user_id', 'u.name', 'u.last_name')
      ->orderBy('reprint_count', 'DESC')
      ->limit(5)
      ->get();

    $salesByUser = (clone $baseQuery)
      ->select(
        'vpl.user_id',
        DB::raw("CONCAT(COALESCE(u.name, ''), ' ', COALESCE(u.last_name, '')) as user_name"),
        's.id as sale_id',
        's.fk_cliente_id',
        's.folio',
        DB::raw('COUNT(vpl.id) as reprint_events'),
        DB::raw('MAX(vpl.created_at) as last_reprint_at')
      )
      ->groupBy('vpl.user_id', 'u.name', 'u.last_name', 's.id', 's.fk_cliente_id', 's.folio')
      ->orderBy('last_reprint_at', 'DESC')
      ->get();

    $saleIds = [];
    foreach ($salesByUser as $row) {
      $saleIds[] = (int) $row->sale_id;
    }

    $productsBySale = [];
    if (count($saleIds) > 0) {
      $productRows = DB::table('sales_item')
        ->select(
          'fk_sales_id',
          DB::raw('COUNT(*) as lines_count'),
          DB::raw('GROUP_CONCAT(DISTINCT name_item ORDER BY name_item SEPARATOR " | ") as products')
        )
        ->whereIn('fk_sales_id', $saleIds)
        ->groupBy('fk_sales_id')
        ->get();

      foreach ($productRows as $p) {
        $productsBySale[(int) $p->fk_sales_id] = [
          'lines_count' => (int) $p->lines_count,
          'products' => $p->products,
        ];
      }
    }

    $users = [];
    $userSalesRows = [];
    foreach ($salesByUser as $row) {
      $key = (string) $row->user_id;
      if (!isset($users[$key])) {
        $users[$key] = [
          'user_id' => $row->user_id,
          'user_name' => trim($row->user_name) !== '' ? $row->user_name : ('ID ' . $row->user_id),
          'total_reprints' => 0,
          'sales' => [],
        ];
      }

      $users[$key]['total_reprints'] += (int) $row->reprint_events;

      $productData = isset($productsBySale[(int) $row->sale_id]) ? $productsBySale[(int) $row->sale_id] : ['lines_count' => 0, 'products' => ''];

      $users[$key]['sales'][] = [
        'sale_id' => (int) $row->sale_id,
        'client_id' => $row->fk_cliente_id,
        'folio' => $row->folio,
        'reprint_events' => (int) $row->reprint_events,
        'last_reprint_at' => $row->last_reprint_at,
        'products' => $productData['products'],
        'lines_count' => $productData['lines_count'],
      ];

      $userSalesRows[] = [
        'user_id' => $row->user_id,
        'user_name' => trim($row->user_name) !== '' ? $row->user_name : ('ID ' . $row->user_id),
        'sale_id' => (int) $row->sale_id,
        'client_id' => $row->fk_cliente_id,
        'folio' => $row->folio,
        'reprint_events' => (int) $row->reprint_events,
        'last_reprint_at' => $row->last_reprint_at,
        'products' => $productData['products'],
        'lines_count' => $productData['lines_count'],
      ];
    }

    $users = array_values($users);
    usort($users, function ($a, $b) {
      return $b['total_reprints'] <=> $a['total_reprints'];
    });

    return response()->json([
      'top_reprinters' => $topReprinters,
      'users' => $users,
      'user_sales' => $userSalesRows,
    ]);
  }

  public function saleDetails($saleID) {
    $query = DB::table('sales_item')
    ->where('fk_sales_id', $saleID)
    ->get();

    $subQuery = DB::table('sales')
    ->where('fk_shop_id', Auth::user()->shop)
    ->where('id', $saleID)
    ->get();

    return response()->json(['sales_item' => $query, 'sale' => $subQuery]);
  }

  public function store(Request $request) {
      $query = DB::table('sales')->insertGetId(
        [
          'payment_method' => $request['payment_method'],
          'discount_type' => $request['discount_type'],
          'discount' =>  $request['discount'],
          'amount' =>  $request['amount'],
          'fk_cliente_id' => $request['fk_cliente_id'],
          'urlpdf' => $request['urlpdf'],
          'folio' => $request['folio'],
          'informedSii' => $request['informedSii'],
          'type_document' => $request['type_document'],
          'fk_user_id' =>  Auth::id(),
          'fk_shop_id' => Auth::user()->shop
        ]
      );
      return response()->json(['id' => $query], 200);
  }

  public function AddSold($code, $quantity){
    $update = DB::table('items')
    ->where('fk_user_id', $this->userID)
    ->where('code', $code)
    ->increment('sold', $quantity);
  }

  public function inserItemsToSale(Request $request) {
      $items = $request['items'];
      try {
        foreach ($items as $item) {
          DB::table('sales_item')->insert(
            [
              'fk_sales_id' => $request['fk_sales_id'],
              'code' => $item['details']['code'] ? $item['details']['code'] : 0,
              'name_item' =>  $item['details']['name'],
              'price' =>  $item['details']['price'],
              'quantity' => $item['quantity'],
            ]
          );
        } 
        return response()->json(['status' => 'OK']);
      } catch (\Exception $e) {
        return $e->getMessage();
      }
  }

  public function items(Request $request) {
    try {
      if($request['code']) {
        $this->AddSold($request['code'], $request['quantity']);
      }
      $query = DB::table('sales_item')->insert(
          [
            'fk_sales_id' => $request['fk_sales_id'],
            'code' => $request['code'] ? $request['code'] : 0,
            'name_item' =>  $request['name_item'],
            'price' =>  $request['price'],
            'quantity' => $request['quantity'],
          ]
        );
        return $query;
    } catch (\Exception $e) {
      return response()->json(['error' => 'json invalid']);
    }
  }

  public function destroy($id){
    if(Auth::user()->fk_id_user_type == 2) {
      $query = DB::table('sales')
      ->where('fk_user_id', Auth::id())
      ->where('id', $id)->delete();
      return response()->json('success', 200);
    }
    else {
      $query = DB::table('sales')
      ->where('fk_shop_id', Auth::user()->shop)
      ->where('id', $id)->delete();
      return response()->json('success', 200);
    }
  }
}
