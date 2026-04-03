<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Str;
use Auth;
use DB;

class ShoppingCartController extends Controller
{
    //
    public function show() {
      $query = DB::table('shopping_cart_name')
      ->where('fk_user_id', Auth::id())
      ->get();
      return $query;
    }

    public function returnShop($token){
        $query = DB::table('shopping_carts')
        ->where('token', $token)
        ->get();
        $delete = DB::table('shopping_carts')
        ->where('token', $token)
        ->delete();
        $delete_shop = DB::table('shopping_cart_name')
        ->where('token', $token)
        ->delete();
        return $query;
    }


    public function getIDfromCode($code) {
        $query = DB::table('items')
        ->select('id')
        ->where('code', $code)->first();
        return $query->id;
    }

    public function SaveShop(Request $request, $commit){

      define('TOKEN', Str::random(20));

      // LIMITAR QUE NO SE REPITA POR USUARIO
      $ifExist = DB::table('shopping_cart_name')
      ->where('fk_user_id', Auth::id())
      ->where('commentary', $commit)->count();

      if($ifExist >= 1) { // SI HAY EXISTENCIA ENVIAMOS NAME = TRUE
        return response()->json(['name' => true]);
      }
      else {
        $saveShop = DB::table('shopping_cart_name')
        ->insert(['commentary' => $commit, 'token' => TOKEN, 'fk_user_id' => Auth::id()]);

        if($saveShop) {
          foreach ($request->all() as $key => $value) {
              $query = DB::table('shopping_carts')->insert(
                [
                  'token' => TOKEN,
                  'code' =>   $value['details']['code'],
                  'fk_id_item' => $this->getIDfromCode($value['details']['code']),
                  'name' =>    $value['details']['name'],
                  'price' =>   $value['details']['price'],
                  'quantity' =>  $value['quantity'],
                ]
              );
            }
         }
       }
    }


}
