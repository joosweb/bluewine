<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;


class DailyBoxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $userID;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->fk_id_user_type == 1) {
                $this->userID = Auth::id(); // IS ADMIN
            } else {
                $query = DB::table('shop')
                    ->select('fk_user_id_admin')
                    ->where('id', Auth::user()->shop)
                    ->first();
                $this->userID = $query->fk_user_id_admin;
            }
            return $next($request);
        });
    }

    // ganancia por item
    public function getDifferenceItemPrice($code, $quantity)
    {
        $item = DB::table('items')
            ->select('price', 'purchase_price')
            ->where('code', $code)
            ->first();
        $revenue = (@$item->price - @$item->purchase_price) * @$quantity;
        return $revenue;
    }

    public function getTotalForItem($code)
    {
        $item = DB::table('items')
            ->select('price', 'purchase_price')
            ->where('code', $code)
            ->first();
        $revenue = $item->price - $item->purchase_price;
        return $revenue;
    }

    public function salesForMethod($isBetween = false, $time_start = null, $time_end = null)
    {
        $efectivo = 0;
        $credit = 0;
        $debito = 0;
        $transferencia = 0;
        
        $user_id = '';
        
        if(Auth::user()->fk_id_user_type == 2) {
            $user_id = Auth::id();
        }

        $sales = DB::table('sales');
            $sales->select('amount', 'payment_method');
            $sales->whereDate('created_at', '=', DB::raw('curdate()'));
            if($user_id){
                $sales->where('fk_user_id', $user_id);
            }
        if ($isBetween) {
            $sales->whereTime('created_at', '>=', $time_start);
            $sales->whereTime('created_at', '<=', $time_end);
        }


        foreach ($sales->get() as $sale) {
            if ($sale->payment_method == 1) {
                $efectivo += $sale->amount;
            } elseif ($sale->payment_method == 2) {
                $credit += $sale->amount;
            } elseif ($sale->payment_method == 6) {
                $debito += $sale->amount;
            } elseif ($sale->payment_method == 8) {
                $transferencia += $sale->amount;
            }
        }

        $total = $efectivo + $debito + $credit + $transferencia;

        return compact(
            'efectivo',
            'credit',
            'debito',
            'transferencia',
            'total'
        );
    }

    // ganancia por venta
    public function calculateRevenue($sales_id)
    {
        $revenue = 0;
        $sales_item = DB::table('sales_item')
            ->select('code', 'quantity')
            ->where('fk_sales_id', $sales_id)
            ->get();
        foreach ($sales_item as $item) {
            $revenue += $this->getDifferenceItemPrice(
                $item->code,
                $item->quantity
            );
        }
        return $revenue;
    }

    public function closeTurn()
    {
        try {
            $box_today = DB::table('daily_box')
                ->where('status', 'OPEN')
                ->where('fk_user_id', $this->userID)
                ->whereDate('created_at', '=', DB::raw('curdate()'))
                ->first();

            $time_start = Carbon::parse($box_today->created_at)->format('H:i:s');
            $time_end = Carbon::now()->format('H:i:s');
            $initial_balance = $box_today->daily_box;

            
            $expenses = DB::table('expenses')
                ->select(DB::raw('SUM(amount) as total'))
                ->where('fk_user_id', $this->userID)
                ->whereDate('created_at', '=', Carbon::parse($box_today->created_at)->format('Y-m-d'))
                ->whereTime('created_at', '>=', $time_start)
                ->whereTime('created_at', '<=', $time_end)
                ->first();

            $summary = $this->salesForMethod(true, Carbon::parse($time_start)->toTimeString(), Carbon::parse($time_end)->toTimeString()); // resumen por metodo de pago
            
            $sales = DB::table('sales') // ventas
                ->where('fk_user_id', $this->userID)
                ->select('id')
                ->whereDate('created_at', Carbon::parse($box_today->created_at)->format('Y-m-d'))
                ->whereTime('created_at', '>=', $time_start)
                ->whereTime('created_at', '<=', $time_end)
                ->get();

            $total_profit = 0;
            
            foreach ($sales as $sale) { // calcular ganancia
                $total_profit += $this->calculateRevenue($sale->id);
            }

            $total_general = $box_today->daily_box - $expenses->total + $summary['total'];
            $cash_on_hand = $box_today->daily_box - $expenses->total + $summary['efectivo'];

            DB::table('daily_box_closures')->insert([ // guardar cierre
                'time_start' => $time_start,
                'time_end' => $time_end,
                'initial_balance' => $initial_balance ? $initial_balance : 0,
                'expenses' => $expenses->total ? $expenses->total : 0,
                'cash' => $summary['efectivo'] ? $summary['efectivo'] : 0,
                'credit' => $summary['credit'] ? $summary['credit'] : 0,
                'debit' => $summary['debito'] ? $summary['debito'] : 0,
                'transfer' => $summary['transferencia'] ? $summary['transferencia'] : 0,
                'total' => $summary['total'] ? $summary['total'] : 0,
                'total_profit' => $total_profit ? $total_profit : 0,
                'total_general' => $total_general ? $total_general : 0,
                'cash_on_hand' => $cash_on_hand ? $cash_on_hand : 0,
                'user_id' => $this->userID
            ]);

            DB::table('daily_box') // ACTUALIZAR CAJA
                ->where('fk_user_id', $this->userID)
                ->where('status', 'OPEN')
                ->whereDate('created_at', '=', DB::raw('curdate()'))
                ->update(['created_at' => Carbon::now(), 'status' => 'CLOSE']);

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getFromPaymentMethod($method_id)
    {
        return DB::table('sales')->where('fk_user_id', $this->userID)
            ->whereDate('created_at', '=', DB::raw('curdate()'))
            ->where('payment_method', $method_id)
            ->sum('amount');
    }

    public function dailySummaryTurn()
    {   
        $revenue = 0;
        $user_id = '';
        
        if(Auth::user()->fk_id_user_type == 2) {
            $user_id = Auth::id();
        }
        
        $find_daily_box_today = DB::table('daily_box')
            ->where('status', 'OPEN')
            ->where('fk_user_id', $this->userID)
            ->whereDate('created_at', '=', DB::raw('curdate()'))
            ->first();
        $time_start = Carbon::parse(@$find_daily_box_today->created_at)->format('H:i:s');
        $time_end = Carbon::now()->format('H:i:s');
        $daily_box = $find_daily_box_today->daily_box;

        $expenses = DB::table('expenses')
            ->whereTime('created_at', '>=', $time_start)
            ->whereTime('created_at', '<=', $time_end)
            ->sum('amount');
   
        
        $sales = DB::table('sales');
            if($user_id){
                $sales->where('fk_user_id', $user_id);
            }
            $sales->select('id');
            $sales->whereDate('created_at', '=', DB::raw('curdate()'));
            $sales->whereTime('created_at', '>=', $time_start);
            $sales->whereTime('created_at', '<=', $time_end);


        @$sales_for_type_payment = $this->salesForMethod(true, $time_start, $time_end);

        foreach ($sales->get() as $sale) {
            $revenue += $this->calculateRevenue($sale->id);
        }
        return view(
            'daily.index',
            compact(
                'time_start',
                'time_end',
                'daily_box',
                'expenses',
                'revenue',
                'sales_for_type_payment'
            )
        );
    }

    public function checkExistDaily()
    {
        $handleIsOpen = DB::table('daily_box')
            ->where('status', 'OPEN')
            ->whereDate('created_at', '=', DB::raw('curdate()'))
            ->first();
        if ($handleIsOpen) {
            return true;
        }
    }

    public function index()
    {
        try {
            if ($this->checkExistDaily()) {
                return response()->json(['status' => true]);
            } else {
                return response()->json(['status' => false]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function getBoxs(Request $request)
    {   
        $boxs = DB::table('daily_box_closures')
            ->where('user_id', $this->userID);
            if($request['date']) {
                $boxs->whereDate('created_at', '=', $request['date']);
            } else {
                $boxs->whereDate('created_at', '=', Carbon::now());
            }

        return $boxs->get();
    }

    public function showBox(Request $request, $boxID)
    {
        $boxID = base64_decode($boxID);;
        $box = DB::table('daily_box_closures')->where('id', $boxID)->first();
        $sales = DB::table('sales')
            ->select('users.name', 'users.last_name', 'sales.payment_method', 'sales.amount', 'sales.discount', 'sales.created_at')
            ->where('fk_user_id', $this->userID)
            ->join('users', 'users.id', '=', 'sales.fk_user_id');
            if($request['date']) {
                $sales->whereDate('sales.created_at', '=', $request['date']);
            } else {
                $sales->whereDate('sales.created_at', '=', DB::raw('curdate()'));
            }
            $sales->whereTime('sales.created_at', '>=', $box->time_start);
            $sales->whereTime('sales.created_at', '<=', $box->time_end);

        return response()->json(['box' => $box, 'sales' => $sales->paginate(7)]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        try {
            if (!$this->checkExistDaily()) {
                $createInitialDaily = DB::table('daily_box')->insert([
                    'daily_box' => $request->input_initial_daily,
                    'status' => 'OPEN',
                    'fk_user_id' => $this->userID
                ]);
                if ($createInitialDaily) {
                    return response()->json([
                        'status' => true,
                        'msg' =>
                        'La caja inicial se ha creado satisfactoriamente.',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'msg' => 'La caja inicial ya existe',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => $e->getMessage()]);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
