<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class CheckAuthController extends Controller
{
    public function check() {
      if (Auth::check()) {
        if(Auth::user()->fk_id_user_type == 1) {
          return response()->json(['login'=> true, 'isadmin' => true, 'id' => Auth::id(), 'shop' => Auth::user()->shop]);
        }
        else {
          return response()->json(['login'=> true, 'isadmin' => false, 'shop' => Auth::user()->shop]);
        }
      }
      else {
          return response()->json(['login'=> false]);
      }
   }
}
