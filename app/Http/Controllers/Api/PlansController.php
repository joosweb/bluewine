<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;

class PlansController extends Controller
{
    //
    public function getLimitplan($planID, $x_max){
        $query = DB::table('plans_config')->where('id_fk_plan', $planID)
        ->select($x_max)->first();
        return $query->$x_max;
    }

    public function index() {

        $query = DB::table('plans as T1')
        ->leftJoin('plans_config as T2','T1.id', '=', 'T2.id_fk_plan')
        ->where('T1.id', Auth::User()->fk_id_plan)
        ->first();

        $clients = DB::table('clients')->where('fk_user_id', Auth::id())->count();

        return response()->json(
          [
            'name' => $query->name,
            'clients' => $clients,
            'clients_max' => $query->clients_max,
            'items_max' => $query->items_max,
            'prov_max' => $query->prov_max,
            'cat_max' => $query->cat_max,
            'sellers_max' => $query->sellers_max,
            'price' => number_format($query->price),
          ]
        );

    }
}
