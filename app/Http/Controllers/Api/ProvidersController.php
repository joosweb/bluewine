<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Malahierba\ChileRut\ChileRut;
use Malahierba\ChileRut\Rules\ValidChileanRut;
use Auth;
use DB;


class ProvidersController extends Controller
{
    private $userID;
    private $proviersMAX;

    public function __construct(){
      $this->middleware('auth');
      $this->middleware(function ($request, $next) {
      $plan = new PlansController(); // getplanlimit instance
      $this->providersMAX = $plan->getLimitplan(Auth::user()->fk_id_plan, 'prov_max');
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

    public function autocomplete(Request $request) {
      $query = DB::table('providers')
      ->select('id','name','last_name')
      ->where('fk_user_id', $this->userID)
      ->where('run', 'LIKE', '%'.$request['q'].'%')
      ->limit(10)
      ->get();

      return json_encode($query);
    }

    public function SearchClient($id) {
      $query = DB::table('providers')
      ->select('id','name','last_name','email','phone')
      ->where('fk_user_id', $this->userID)
      ->where('id', $id)
      ->first();

      return json_encode($query);
    }

    public function index() {
      //sleep(4);
      $query = DB::table("providers")->where('fk_user_id', $this->userID)->paginate(8);
      return $query;
    }

    public function show($filter, $value) {
      if($value == "vacio") {
        $clients = DB::table('providers')
        ->where('fk_user_id', $this->userID)
        ->paginate(8);
        return $clients;
      }
      else if($filter == 'name') {
        $query = DB::table('providers')
        ->where('fk_user_id', $this->userID)
        ->where($filter, 'like', '%' . $value . '%')->paginate(8);
        return $query;
      }
      else if($filter == 'run') {
        $query = DB::table('providers')
        ->where('fk_user_id', $this->userID)
        ->where('run', 'like', '%' . $value . '%')
        ->paginate(8);
        return $query;
      }
      else {
        $query = DB::table('providers')
        ->where('fk_user_id', $this->userID)
        ->where('id', $value)
        ->get();

        return $query;
      }
   }

   public function store(Request $request)
    {
        $providers = DB::table('providers')->where('fk_user_id', $this->userID)->count();
        if($this->providersMAX == 0 || $providers < $this->providersMAX){
          $run_sin_puntos = str_replace(".", "", $request['run']);
          $run_sin_puntos = str_replace("-", "", $run_sin_puntos);
          $run_sin_puntos = substr($run_sin_puntos, 0, -1);

          $query = DB::table('providers')
          ->select('id')
          ->where('fk_user_id', $this->userID)
          ->where('run', intval($run_sin_puntos))->count();

          $validator = $this->validate($request,[
              'run' => ['required', 'string', new ValidChileanRut(new ChileRut)],
              'last_name' => 'required|string|max:150',
              'email' => 'required|email|unique:providers,email,NULL,id,fk_user_id,'.$this->userID,
              'name' => 'required|string|max:150',
              'phone' => 'string|max:150'
          ]
          );

          if($query) {
            throw  \Illuminate\Validation\ValidationException::withMessages([
             'run' => 'El RUN ingresado ya existe.'
           ]);
          }

          $digito_verificador = substr($request['run'], -1);

          $user_insert = DB::table('providers')
          ->insert(
            [
              'run' => intval($run_sin_puntos),
              'verifying_digit' => $digito_verificador,
              'name' => $request['name'],
              'last_name' => $request['last_name'],
              'email' => $request['email'],
              'phone' => $request['phone'],
              'fk_user_id' => $this->userID
            ]
          );
          response()->json(['success' => true, 200]);
        }
        else {
          return response()->json(['success' => false], 200);
        }
    }

   public function update(Request $request, $id)
    {

        $this->validate($request,[
            'last_name' => 'required|string|max:150',
            'email' => 'required|email|unique:providers,email,NULL,id,fk_user_id,'.$id,
            'name' => 'required|string|max:150',
            'phone' => 'string|max:150'
        ]);

        $run_sin_puntos = str_replace(".", "", $request['run']);
        $digito_verificador = substr($request['verifying_digit'], -1);

        $user_update = DB::table('providers')
        ->where('fk_user_id', $this->userID)
        ->where('id', $id)
        ->update(
          [
            'run' => intval($run_sin_puntos),
            'verifying_digit' => $digito_verificador,
            'name' => $request['name'],
            'last_name' => $request['last_name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'updated_at' => date("Y-m-d H:i:s")
          ]
        );

        /*$this->validate($request,[
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email,'.$user->id,
            'password' => 'sometimes|min:6'
        ]);*/

        //$user->update($request->all());
        return response()->json('success', 200);
    }

   public function destroy($id){
     $delete = DB::table('providers')
     ->where('fk_user_id', $this->userID)
     ->where('id', $id)->delete();
     return response()->json('success', 200);
   }
}
