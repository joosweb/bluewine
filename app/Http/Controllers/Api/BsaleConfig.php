<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;

class BsaleConfig extends Controller
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

    public function index()
    {
        //
        $query = DB::table('bsale_config')->where('fk_user_id', $this->userID)->first();
        return response()->json($query);
    }

    public function store(Request $request)
    {
        // if exist config update or create
        $find_config = DB::table('bsale_config')->where('fk_user_id', $this->userID)->count();

        if($find_config){
          $query = DB::table('bsale_config')
          ->where('fk_user_id', $this->userID)
          ->update(
            [
              'url_documents' => $request['url_documents'],
              'invoice_id_letter' => $request['invoice_id_letter'],
              'invoice_id_thermal' => $request['invoice_id_thermal'],
              'ticket_id_letter' => $request['ticket_id_letter'],
              'ticket_id_thermal' => $request['ticket_id_thermal'],
              'quotation_id_letter' => $request['quotation_id_letter'],
              'token_production' => $request['token_production'],
              'token_certification' => $request['token_certification']
            ]
          );
        }
        else {
          $query = DB::table('bsale_config')->insert(
            [
              'fk_user_id' => $this->userID,
              'url_documents' => $request['url_documents'],
              'invoice_id_letter' => $request['invoice_id_letter'],
              'invoice_id_thermal' => $request['invoice_id_thermal'],
              'ticket_id_letter' => $request['ticket_id_letter'],
              'ticket_id_thermal' => $request['ticket_id_thermal'],
              'token_production' => $request['token_production'],
              'token_certification' => $request['token_certification']
            ]
          );
        }

        return response()->json(['save' => true]);
    }

}
