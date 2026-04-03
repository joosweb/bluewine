<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;

class PosController extends Controller
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

    public function items($category = false, $filter = false, $texto = false) {
      if($category && $filter && $texto) {
        $items = DB::table('items')
        ->select('id','code','name','price', 'photo', 'stock')
        ->where('category_id', $category)
        ->where('fk_user_id', $this->userID)
        ->where($filter, 'LIKE', '%' . strtolower($texto) . '%')
        ->paginate(12);
        return $items;
      }
      else {
        $items = DB::table('items')
        ->select('id','code','name','price', 'photo','stock')
        ->where('fk_user_id', Auth::id())
        ->paginate(12);
        return $items;
      }
    }


    public function itemCategoryF($filter = false, $text = false) {
      if($filter && $text) {
        $items = DB::table('items')
        ->select('id','code','name', 'type_price','price','photo','stock')
        ->where('fk_user_id', $this->userID)
        ->where($filter, 'LIKE', '%' . strtolower($text) . '%')
        ->paginate(12);
        return $items;
      }
      else {
        $items = DB::table('items')
        ->select('id','code','name','type_price','price','photo','stock')
        ->where('fk_user_id', Auth::id())
        ->paginate(12);
        return $items;
      }
    }

    public function stock($code) {
      $query = DB::table('items')
      ->select('stock')
      ->where('fk_user_id', $this->userID)
      ->where('code', $code)
      ->first();

      return response()->json(['stock' => $query->stock]);
    }

    public function setstock($id, $quantity){
      $operation = DB::table('items')
      ->where('fk_user_id', $this->userID)
      ->where('id', $id)
      ->update(['stock' => $quantity]);

      return response()->json(['stock' => 'OK']);
    }

    public function IncrementDecrementStock($code, $quantity = 1, $operation){

        $stock = 0;

        $query = DB::table('items')
        ->select('stock')
        ->where('fk_user_id', $this->userID)
        ->where('code', $code)
        ->first();

        if($operation == 1) // suma
        {
            $stock = $query->stock + $quantity;
            DB::table('items')
            ->where('fk_user_id', $this->userID)
            ->where('code', $code)
            ->increment('stock', $quantity);
        }
        else if($query->stock != 0){
            $stock = $query->stock - $quantity;
            DB::table('items')
            ->where('fk_user_id', $this->userID)
            ->where('code', $code)
            ->decrement('stock', $quantity);
        }

        return response()->json(['stock' => $stock]);
    }

    public function GetItemForCode($code){
          $items = DB::table('items')
          ->select('id','code','name','price','type_price','photo','stock')
          ->where('fk_user_id', $this->userID)
          ->where('code', $code)
          ->first();

          if(empty($items)) {
            return response()->json(['item' => false]);
          }
          else {
            return response()->json(['item' => $items]);
          }

     }
}
