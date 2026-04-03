<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExpensesExport;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;

class ExpensesController extends Controller
{
    private $userID;
    private $isAdmin;

    public function __construct(){
      $this->middleware('auth');
      $this->middleware(function ($request, $next) {
      if(Auth::user()->fk_id_user_type == 1)
      {
        $this->isAdmin = true;
        $this->userID = Auth::id(); // IS ADMIN
      }
      else {
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

    public function excel($filter,$text,$from,$to)
    {
      if(!$filter && !$from && !$to){
        return response()->json(['search' => false]);
      }
      else {
        return Excel::download(new ExpensesExport($filter,$text,$from,$to), 'products.xlsx');
      }
    }

    public function index()
    {
      if($this->isAdmin) {
        $query = DB::table('expenses as T1')
        ->select('T2.id as userID','T1.id','T1.fk_user_run','T2.name', 'T2.last_name','T2.run','T1.amount','T1.commentary','T1.created_at')
        ->LeftJoin('users as T2', 'T1.fk_user_id', '=', 'T2.id')
        ->where('T2.shop', Auth::user()->shop)
        ->orderBy('T1.created_at', 'DESC')
        ->paginate(8);
        return response()->json(['data' => $query, 'admin' => $this->userID]);
      }
      else {
        $query = DB::table('expenses as T1')
        ->select('T2.id as userID','T1.id','T1.fk_user_run','T2.name', 'T2.last_name','T2.run','T1.amount','T1.commentary','T1.created_at')
        ->LeftJoin('users as T2', 'T1.fk_user_id', '=', 'T2.id')
        ->where('T1.fk_user_id', Auth::user()->id)
        ->orderBy('T1.created_at', 'DESC')
        ->paginate(8);
        return response()->json(['data' => $query, 'admin' => $this->userID]);
      }

    }

    public function ChartOfDay($from, $to) {
      if($this->isAdmin) {
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
        ->where('fk_user_id', Auth::user()->id)
        ->groupBy('date')
        ->get();
        return $query;
      }
    }

    public function expenses_for_user($filter, $text, $from, $to) {
      if($this->isAdmin) {
        $query = DB::table('expenses as T1')
        ->select(DB::raw('SUM(amount) AS amount'),'T1.fk_user_id', 'T2.name', 'T2.last_name','T1.created_at')
        ->join('users as T2', 'T1.fk_user_id', '=', 'T2.id')
        ->groupBy('T2.name', 'T2.id');
        if($text){
          $query->where($filter, 'LIKE', '%'.$text.'%');
        }
        $query->whereBetween('T1.created_at', [$from, $to]);
        $query->orderBy('amount', 'DESC');
        $query->where('T2.shop', Auth::user()->shop);
        return $query->get();
      }
      else {
        $query = DB::table('expenses as T1')
        ->select(DB::raw('SUM(amount) AS amount'),'T1.fk_user_id', 'T2.name', 'T2.last_name','T1.created_at')
        ->join('users as T2', 'T1.fk_user_id', '=', 'T2.id')
        ->groupBy('T2.name', 'T2.id');
        if($text){
          $query->where($filter, 'LIKE', '%'.$text.'%');
        }
        $query->whereBetween('T1.created_at', [$from, $to]);
        $query->orderBy('amount', 'DESC');
        $query->where('T1.fk_user_id', Auth::user()->id);
        return $query->get();
      }

    }

    public function AdvancedReport($query, $filter, $text, $from, $to){
      $rank_five = $query->orderBy('T1.amount', 'DESC')->limit(10)->get();
      $arrays = [];
      $total = $query->sum('amount');
      array_push($arrays, $rank_five, number_format($total),$this->expenses_for_user($filter, $text, $from, $to));
      return $arrays;
    }

    public function search(Request $request) {
      if(!$request['text'] && !$request['from'] && !$request['to']){
        return response()->json(['search' => false]);
      }
      else {
        $query = DB::table('expenses as T1')
        ->select('T2.id as userID','T1.fk_user_run','T2.name', 'T2.last_name','T2.run','T1.amount','T1.commentary','T1.created_at')
        ->LeftJoin('users as T2', 'T1.fk_user_id', '=', 'T2.id');
        if($request['text']) {
          $query->where('T2.shop', Auth::user()->shop);
          $query->where($request['filter'], 'LIKE', '%'.$request['text'].'%');
        }
        if($request['from'] && $request['to']) {
          $query->whereBetween('T1.created_at', [$request['from'], $request['to']]);
        }
        //$query->orderBy('T1.created_at', 'DESC');
        if($this->isAdmin) {
          $query->where('T2.shop', Auth::user()->shop);
        }
        else {
          $query->where('T1.fk_user_id', Auth::user()->id);
        }
        
        return response()->json(
            [
              'data' => $query->paginate(8),
              'grafic' => $this->ChartOfDay($request['from'], $request['to']),
              'admin' => $this->userID,
              'advanced_reports' => $this->AdvancedReport($query, $request['filter'], $request['text'], $request['from'], $request['to'])
        ]);
      }
    }


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
      try {
        if($request['created_at']) {
          $query = DB::table('expenses')->insert(
            [
              'fk_user_id' => Auth::id(),
              'fk_user_run' => Auth::user()->run,
              'fk_user_shop' => Auth::user()->shop,
              'amount' => $request['amount'],
              'commentary' => $request['commentary'],
              'created_at' => $request['created_at'],
            ]
          );
        } else {
          $query = DB::table('expenses')->insert(
            [
              'fk_user_id' => Auth::id(),
              'fk_user_run' => Auth::user()->run,
              'fk_user_shop' => Auth::user()->shop,
              'amount' => $request['amount'],
              'commentary' => $request['commentary'],
            ]
          );
        }

        return response()->json(['status' => true]);
      } catch (\Exception $e) {
        return response()->json(['error' => $e]);
      }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        try {
          if($request['created_at']) {
            $query = DB::table('expenses')
            ->where('id', $id)
            ->update(
              [
                'amount' => $request['amount'],
                'commentary' => $request['commentary'],
                'created_at' => $request['created_at'],
              ]
            );
          }
          else {
            $query = DB::table('expenses')
            ->where('id', $id)
            ->update(
              [
                'amount' => $request['amount'],
                'commentary' => $request['commentary'],
              ]
            );
          }
          return response()->json(['status' => true]);
        } catch (\Exception $e) {
          return response()->json(['error' => $e]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $query = DB::table('expenses')->where('id', $id)->delete();
        return response()->json(['status' => $query]);
    }
}
