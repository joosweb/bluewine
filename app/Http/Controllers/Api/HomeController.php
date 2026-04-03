<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Auth;

class HomeController extends Controller
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

    public function percentajeC($current_day, $yesterday) {

      $percentage = $current_day * 100;

      if($yesterday - $current_day == 0){
        $percentage = 0;
      }
      else {
        if($yesterday != 0){
          $percentage = $percentage / $yesterday - 100;
        }
      }
        if($percentage < 0) {
          $percentage = round($percentage * -1);
          $msg = $percentage.' % menor al dia anterior.';
        }
        else {
          $msg = round($percentage).' % mayor al dia anterior.';
        }

        $array = array($msg, $percentage);

        return $array;
    }

    public function reportsales() {

      $month = date("m");

      $tickets = DB::table('sales')
      ->whereDate('created_at', Carbon::now())
      ->where('type_document', 1)
      ->count();
      $invoices = DB::table('sales')
      ->whereDate('created_at', Carbon::now())
      ->where('type_document', 2)
      ->count();
      $quotation = DB::table('sales')
      ->whereDate('created_at', Carbon::now())
      ->where('type_document', 3)
      ->count();

      $expenses_day = DB::table('expenses')
      ->select(DB::raw('SUM(amount) as amount'))
      ->whereDate('created_at', Carbon::now())
      ->first()->amount;

      $sales_yesterday = DB::table('sales')
      ->whereDate('created_at', Carbon::yesterday())
      ->where('fk_shop_id', Auth::user()->shop)
      ->count();

      $sales_current = DB::table('sales')
      ->whereDate('created_at', Carbon::now())
      ->where('fk_shop_id', Auth::user()->shop)
      ->count();

      $yesterday = DB::table('sales')
      ->whereDate('created_at', Carbon::yesterday())
      ->where('fk_shop_id', Auth::user()->shop)
      ->sum('amount');

      $current_day = DB::table('sales')
      ->whereDate('created_at', Carbon::now())
      ->where('fk_shop_id', Auth::user()->shop)
      ->sum('amount');

      $percentaje_amount = $this->percentajeC($current_day, $yesterday);
      $percentaje_sales = $this->percentajeC($sales_current, $sales_yesterday);

      return response()->json(
        [
          'current_day' => number_format($current_day),
          'expenses_day' => number_format($expenses_day),
          'sales_current' => $sales_current,
          'amount_percentaje' => $percentaje_amount[0],
          'sales_percentaje' => $percentaje_sales[0],
          'am_p' => $percentaje_amount[1],
          'sa_p' => $percentaje_sales[1],
          'tickets' => $tickets,
          'invoices' => $invoices,
          'quotation' => $quotation,
         ]);
      }

    public function Counters(){
        $clients = DB::table('clients')->where('fk_user_id', $this->userID)
        ->count();
        $providers = DB::table('providers')->where('fk_user_id',$this->userID)
        ->count();
        $items = DB::table('items')->where('fk_user_id',$this->userID)
        ->count();
        $categories = DB::table('categories')->where('fk_user_id',$this->userID)
        ->count();
        $sellers = DB::table('users')->where('shop', Auth::user()->shop)->where('fk_id_user_type', 2)
        ->count();
        return response()->json(
          [
            'clients' => $clients,
            'providers' => $providers,
            'items' => $items,
            'categories' => $categories,
            'sellers' => $sellers
          ]
      );
    }

    public function BestSellers(){
      $query = DB::table('items')
      ->select('photo','name', 'sold')
      ->where('fk_user_id', $this->userID)
      ->orderBy('sold', 'DESC')
      ->limit(5)
      ->get();
      return $query;
    }

    public function ChartOfDay() {
      if(Auth::user()->fk_id_user_type == 1) {
        $query = DB::table('sales')
        ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) AS amount'))
        ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
        ->where('fk_shop_id', Auth::user()->shop)
        ->groupBy('date')
        ->get();
        return $query;
      }
      else {
        $query = DB::table('sales')
        ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) AS amount'))
        ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
        ->where('fk_user_id', Auth::id())
        ->groupBy('date')
        ->get();
        return $query;
      }

    }

    public function ItemsLessStock(){
      $query = DB::table('items')
      ->select('photo', 'code', 'name', 'stock')
      ->where('fk_user_id', $this->userID)
      ->orderBy('stock', 'ASC')
      ->limit(5)
      ->get();
      return $query;
    }

    public function LastDocuments(){
      if(Auth::user()->fk_id_user_type == 1) {
        $sd = DB::table('sales')
        ->select('amount', 'payment_method', 'discount')
        ->where('fk_shop_id', Auth::user()->shop)
        ->where('type_document', 0)
        ->limit(6)
        ->orderBy('created_at', 'DESC')
        ->get();
        $tickets = DB::table('sales')
        ->select('id', 'amount', 'urlpdf', 'informedSii')
        ->where('fk_shop_id', Auth::user()->shop)
        ->where('type_document', 1)
        ->limit(5)
        ->orderBy('created_at', 'DESC')
        ->get();
        $invoices = DB::table('sales')
        ->select('id','amount', 'urlpdf', 'informedSii')
        ->where('fk_shop_id', Auth::user()->shop)
        ->where('type_document', 2)
        ->limit(5)
        ->orderBy('created_at', 'DESC')
        ->get();
      }
      else {
        $sd = DB::table('sales')
        ->select('amount', 'payment_method', 'discount')
        ->where('fk_user_id', Auth::id())
        ->where('type_document', 0)
        ->limit(6)
        ->orderBy('created_at', 'DESC')
        ->get();

        $tickets = DB::table('sales')
        ->select('amount', 'urlpdf', 'informedSii')
        ->where('fk_shop_id', Auth::id())
        ->where('type_document', 1)
        ->limit(5)
        ->orderBy('created_at', 'DESC')
        ->get();

        $invoices = DB::table('sales')
        ->select('amount', 'urlpdf', 'informedSii')
        ->where('fk_shop_id', Auth::id())
        ->where('type_document', 2)
        ->limit(5)
        ->orderBy('created_at', 'DESC')
        ->get();
      }



      return response()->json(
        [
          'process' => $sd,
          'tickets' => $tickets,
          'invoices' => $invoices
        ]
      );
    }
}
