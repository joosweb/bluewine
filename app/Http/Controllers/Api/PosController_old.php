<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;

class PosController extends Controller
{

    public function items($category = false, $filter = false, $texto = false) {
      if($category && $filter && $texto) {
        $items = DB::table('items')
        ->select('id','code','name','price', 'photo', 'stock')
        ->where('category_id', $category)
        ->where('fk_user_id', Auth::id())
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
        ->select('id','code','name','price','photo','stock')
        ->where('fk_user_id', Auth::id())
        ->where($filter, 'LIKE', '%' . strtolower($text) . '%')
        ->paginate(12);
        return $items;
      }
      else {
        $items = DB::table('items')
        ->select('id','code','name','price','photo','stock')
        ->where('fk_user_id', Auth::id())
        ->paginate(12);
        return $items;
      }
    }

    public function stock($code) {
      $query = DB::table('items')
      ->select('stock')
      ->where('fk_user_id', Auth::id())
      ->where('code', $code)
      ->first();

      return response()->json(['stock' => $query->stock]);
    }

    public function setstock($id, $quantity){
      $operation = DB::table('items')
      ->where('fk_user_id', Auth::id())
      ->where('id', $id)
      ->update(['stock' => $quantity]);

      return response()->json(['stock' => 'OK']);
    }

    public function IncrementDecrementStock($code, $quantity = 1, $operation){

        $stock = 0;

        $query = DB::table('items')
        ->select('stock')
        ->where('fk_user_id', Auth::id())
        ->where('code', $code)
        ->first();

        if($operation == 1) // suma
        {
          $stock = $query->stock + $quantity;
        }
        else if($query->stock != 0) {
          $stock = $query->stock - $quantity;
        }

        $operation = DB::table('items')
        ->where('fk_user_id', Auth::id())
        ->where('code', $code)
        ->update(['stock' => $stock]);

        return response()->json(['stock' => $stock]);
    }

    public function GetItemForCode($code){


          $items = DB::table('items')
          ->select('id','code','name','price','photo','stock')
          ->where('fk_user_id', Auth::id())
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
