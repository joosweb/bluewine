<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;

class PageConfig extends Controller
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
      $query = DB::table('page_config')->where('fk_user_id', $this->userID)->first();
      return response()->json($query);
    }

    public function name()
    {
      $query = DB::table('page_config')
      ->select('name_ecommerce')
      ->where('fk_user_id', $this->userID)
      ->first();
      return response()->json($query);
    }

    public function getConfigStock(){
      $find_config = DB::table('critical_config')->where('fk_user_id', $this->userID)->first();
      return response()->json($find_config);
    }

    public function criticalStock(Request $request) {
      $find_config = DB::table('critical_config')->where('fk_user_id', $this->userID)->count();
      if($find_config){
        $update = DB::table('critical_config')->where('fk_user_id', $this->userID)->update(
          [
            'notification_status' => $request['notification_status'],
            'notification_email' => $request['notification_email'],
            'notification_range' => $request['notification_range']
          ]
        );
      }
      else {
        $insert = DB::table('critical_config')->insert(
          [
            'fk_user_id' => $this->userID,
            'notification_status' => $request['notification_status'],
            'notification_email' => $request['notification_email'],
            'notification_range' => $request['notification_range']
          ]
        );
      }

      return response()->json(['save' => true]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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
      // if exist config update or create
      $find_config = DB::table('page_config')->where('fk_user_id', $this->userID)->count();

      if($find_config){
        $query = DB::table('page_config')
        ->where('fk_user_id', $this->userID)
        ->update(
          [
            'name_ecommerce' => $request['name_ecommerce'],
            'company_billing' => $request['company_billing'],
            'voucher_logo' => $request['voucher_logo'],
            'continuous_paper_type' => $request['continuous_paper_type'],
            'type_of_environment' => $request['type_of_environment'],
            'ticket_default_format' => $request['ticket_default_format'],
            'invoice_default_format' => $request['invoice_default_format'],
            'default_document_type' => $request['default_document_type'],
            'default_payment_method' => $request['default_payment_method'],
            'printer' => $request['printer'],
            'optional_printer' => $request['optional_printer'],
            'printer_name' => $request['printer_name'],
            'default_css' => $request['default_css_config'],
          ]
        );
      }
      else {
        $query = DB::table('page_config')->insert(
          [
            'fk_user_id' => $this->userID,
            'name_ecommerce' => $request['name_ecommerce'],
            'company_billing' => $request['company_billing'],
            'type_of_environment' => $request['type_of_environment'],
            'ticket_default_format' => $request['ticket_default_format'],
            'invoice_default_format' => $request['invoice_default_format'],
            'default_document_type' => $request['default_document_type'],
            'default_payment_method' => $request['default_payment_method'],
          ]
        );
      }

      return response()->json(['save' => true]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
      $query = DB::table('page_config')
      ->select('default_document_type', 'default_payment_method')
      ->where('fk_user_id', $this->userID)
      ->first();
      return response()->json($query);
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
