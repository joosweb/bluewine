<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Malahierba\ChileRut\ChileRut;
use Malahierba\ChileRut\Rules\ValidChileanRut;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;
use Auth;
use DB;
use Mail;
use App\User;

class SellersController extends PlansController
{
    private $userID;
    private $sellersMAX;

    public function __construct(){
      $this->middleware('auth');
      $this->middleware(function ($request, $next) {
      $plan = new PlansController(); // getplanlimit instance
      $this->sellersMAX = $plan->getLimitplan(Auth::user()->fk_id_plan, 'sellers_max');
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

    public function getsales($id) {
      $query = DB::table('sales')->where('fk_user_id', $id)->count();
      return $query;
    }

    public function index()
    {
        $query = DB::table('users')
        ->select('id','run','verifying_digit','name','last_name','email','phone','created_at','updated_at','photo','status')
        ->where('shop', Auth::user()->shop)
        ->where('fk_id_user_type', 2)
        ->paginate(7);

        return $query;
    }

    public function sendEmail($name, $email, $password)
    {
      $to_name = $name;
      $to_email = $email;
      $data = array('email' => $email,'password' => $password);
      // ENVIAR MAIL
      $email = Mail::send('email.newSeller', $data, function ($message) use ($to_name, $to_email) {
        $message->to($to_email, $to_name)
                ->subject('Bienvenido a OSAN-POS');
        $message->from('noreply@osan.cl', 'OSAN-POS');
      });
    }

    public function store(Request $request)
    {
        $sellers = DB::table('users')->where('fk_id_user_type', 2)
        ->where('shop', Auth::user()->shop)->count();
        if($this->sellersMAX == 0 || $sellers < $this->sellersMAX){
          define('PASSWORD', Str::random(6));

          $formatRut = str_replace('.', '', $request['run']); // quitar puntos
          $formatRut = str_replace('-', '', $formatRut); // quitar guion
          $verifying_digit = substr($request['run'], -1); // digito verificador
          $run = substr($formatRut, 0, -1); // run final

          $query = DB::table('users')
          ->select('id')
          ->where('shop', Auth::user()->shop)
          ->where('run', $run)->count();

          $validator = Validator::make($request->all(), [
              'run' => ['required', 'unique:users,run', 'string', new ValidChileanRut(new ChileRut)],
              'name' => 'required',
              'email' => 'required|email|unique:users,email,NULL,id,shop,'.Auth::user()->shop,
          ]);

          $validator->after(function($validator) use ($query) {
            if($query) {
              $validator->errors()->add('run', 'El run ingresado ya esta en uso');
            }
          });

          if ($validator->fails()) {
              return response()->json(['errors' => $validator->messages()], 200);
          }
          else {
             if(!$request['password']){
               $query = DB::table('users')->insert(
                 [
                   'run' => $run,
                   'verifying_digit' => $verifying_digit,
                   'name' => $request['name'],
                   'last_name' => $request['last_name'],
                   'email' => $request['email'],
                   'password' => Hash::make(PASSWORD),
                   'fk_id_user_type' => 2,
                   'shop' =>  Auth::user()->shop,
                   'status' => $request['status']
                 ]
               );
               if ($request['sendmail']) {
                  $this->sendEmail($request['name'], $request['email'], PASSWORD);
               }
               return response()->json(
                 [
                   'success' => true,
                   'save' => true,
                   'password' => PASSWORD
                  ]
               );
             }
             else {
               $query = DB::table('users')->insert(
                 [
                   'run' => $run,
                   'verifying_digit' => $verifying_digit,
                   'name' => $request['name'],
                   'last_name' => $request['last_name'],
                   'email' => $request['email'],
                   'password' => Hash::make($request['password']),
                   'fk_id_user_type' => 2,
                   'shop' =>  Auth::user()->shop,
                   'status' => $request['status']
                 ]
               );
               if ($request['sendmail'] != 'undefined') {
                  $this->sendEmail($request['name'], $request['email'], $request['password']);
               }
               return response()->json(
                 [
                   'success' => true,
                   'save' => true,
                   'password' => $request['password']
                  ]
               );
             }
          }
        }
        else {
          return response()->json(['success' => false], 200);
        }
    }


    public function show($run)
    {
        $query = DB::table('users')->where('fk_id_user_type', 2)
        ->where('shop', Auth::user()->shop)
        ->where('run', 'LIKE', '%'.$run.'%')
        ->paginate(7);
        return $query;
    }


    public function edit($id)
    {
        //
    }

  
    public function update(Request $request, $id)
    {
      define('PASSWORD', Str::random(6));

      $validator = Validator::make($request->all(), [
          'run' => ['required', 'string', new ValidChileanRut(new ChileRut)],
          'name' => 'required',
          'email' => 'required'
      ]);

      if ($validator->fails()) {
          return response()->json(['errors' => $validator->messages()], 200);
      }
      else {
         $formatRut = str_replace('.', '', $request['run']); // quitar puntos
         $formatRut = str_replace('-', '', $formatRut); // quitar guion
         $verifying_digit = substr($request['run'], -1); // digito verificador
         $run = substr($formatRut, 0, -1); // run final
         if(!$request['password']){
           $query = User::find($id);
           $query->run = $run;
           $query->verifying_digit = $verifying_digit;
           $query->name = $request['name'];
           $query->last_name = $request['last_name'];
           $query->email = $request['email'];
           $query->status = $request['status'];
           $query->save();
           return response()->json(
             [
               'save' => true
              ]
           );
         }
         else {
           if ($request['sendmail'] != 'undefined') {
              $this->sendEmail($request['name'], $request['email'], $request['password']);
           }
           $query = User::find($id);
           $query->run = $run;
           $query->verifying_digit = $verifying_digit;
           $query->name = $request['name'];
           $query->last_name = $request['last_name'];
           $query->email = $request['email'];
           $query->password = bcrypt($request['password']);
           $query->status = $request['status'];
           $query->save();
           return response()->json(
             [
               'save' => true,
               'password' => $request['password']
              ]
           );
         }
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
        $query = DB::table('users')
        ->where('id', $id)
        ->delete();
        return $query;
    }
}
