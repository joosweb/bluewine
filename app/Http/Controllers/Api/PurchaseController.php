<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use File;


class PurchaseController extends Controller
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

    public function totalpurchases() {
      $query = DB::table('purchases')->where('fk_user_id', $this->userID)
      ->count();
      return $query;
    }

    public function search($run) {
      $query = DB::table('purchases as T1')
      ->select('T1.id as pid', 'T1.fk_provider_id','T1.neto','T1.description','T1.created_at','T2.run')
      ->where('T1.fk_user_id', $this->userID)
      ->join('providers as T2', 'T1.fk_provider_id', '=', 'T2.id')
      ->orderBy('T1.created_at', 'DESC')
      ->where('T2.run', 'LIKE', '%'.$run.'%')
      ->paginate(8);
      return $query;
    }

    public function itemdestroy($id) {

      $item = DB::table('purchase_item')
      ->where('id', $id)->first();

      $decrement = DB::table('items')
      ->where('code', $item->code)
      ->where('fk_user_id', $this->userID)
      ->decrement('stock', $item->quantity);

      $query = DB::table('purchase_item')
      ->where('id', $id)
      ->delete();

      return response()->json(['item' => true]);
    }

    public function docdestroy($id) {
        $doc = DB::table('docs')->where('id', $id)->first();
        File::delete($doc->link);
        $query = DB::table('docs')->where('id', $id)->delete();
        return response()->json(['doc' => true]);
    }

    public function index() {
      $query = DB::table('purchases as T1')
      ->select('T1.id as pid', 'T1.fk_provider_id','T1.neto','T1.description','T1.created_at','T2.run')
      ->where('T1.fk_user_id', $this->userID)
      ->join('providers as T2', 'T1.fk_provider_id', '=', 'T2.id')
      ->orderBy('T1.created_at', 'DESC')
      ->paginate(8);
      return $query;
    }

    public function providers(){
      $query = DB::table('providers')
      ->where('fk_user_id', $this->userID)
      ->get();

      return $query;
    }

    public function encrypt_decrypt($action, $string)
    {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = '%tYU&890_+@!~23454A';
        $secret_iv = '%tYU&890_+@!~23454A';
        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } elseif ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }

    public function autocomplete(Request $request) {
       $query = DB::table('items')
       ->where('fk_user_id', $this->userID)
       ->where('name', 'LIKE', '%'.$request['q'].'%')
       ->limit(10)
       ->get();

       return json_encode($query);
    }

    public function checkCode($code)
    {
        //
        $query = DB::table('items')
        ->select('id')
        ->where('fk_user_id', $this->userID)
        ->where('code', $code)
        ->count();

        return response()->json(['item' => $query]);
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

      $query = DB::table('purchases')->insertGetId(
        [
          'fk_provider_id' => $request['provider'],
          'fk_user_id' => $this->userID,
          'neto' => $request['neto'],
          'description' => $request['description']
        ]
       );

       if($request['code']) {
         $countItems = count($request['code']);
         if($countItems >= 1) {
           for ($i=0; $i < $countItems; $i++) {

              // AUMENTAR STOCK
              DB::table('items')
              ->where('fk_user_id', $this->userID)
              ->where('code', $request['code'][$i])
              ->increment('stock', $request['quantity'][$i]);

              // INSERTAR ITEMS
               DB::table('purchase_item')->insert(
                 [
                   'code' => $request['code'][$i],
                   'quantity' => $request['quantity'][$i],
                   'fk_id_purchase' => $query
                 ]
               );
            }
         }
       }

       $authEncrypt = $this->encrypt_decrypt('encrypt', $this->userID);

       if(!File::exists('docs/'.$authEncrypt)) {
          File::makeDirectory('docs/'.$authEncrypt, 0777, true, true);
        }

      if($request->file('files')){
        $uploadedFiles=$request->file('files');
        foreach ($uploadedFiles as $file){
          $file_name = md5(rand(0, 100000)+strtotime(time())).'.'.strtolower($file->getClientOriginalExtension());
          $file->move('docs/'.$authEncrypt, $file_name);
          DB::table('docs')->insert([
            'link' => 'docs/'.$authEncrypt.'/'.$file_name,
            'fk_id_purchase' => $query
          ]);
        }
      }

      return response()->json(['purchase' => true]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $p = DB::table('purchases as T1')
      ->where('T1.fk_user_id', $this->userID)
      ->join('providers as T2', 'T1.fk_provider_id', '=', 'T2.id')
      ->where('T1.id', $id)
      ->get();

      $p_item = DB::table('purchase_item as T1')
      ->select('T1.code', 'T1.quantity', 'T2.name', 'T2.stock')
      ->join('items as T2', 'T1.code', '=', 'T2.code')
      ->where('T1.fk_id_purchase', $id)
      ->get();

      $docs = DB::table('docs')
      ->select('link')
      ->where('fk_id_purchase', $id)
      ->get();

      return response()->json(
        [
          'purchase' => $p,
          'purchase_item' => $p_item,
          'docs' => $docs
        ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $p = DB::table('purchases as T1')
      ->select('T1.id', 'T1.fk_provider_id', 'T1.neto', 'T1.description')
      ->where('T1.fk_user_id', $this->userID)
      ->join('providers as T2', 'T1.fk_provider_id', '=', 'T2.id')
      ->where('T1.id', $id)
      ->first();

      $p_item = DB::table('purchase_item as T1')
      ->select('T1.id','T1.code', 'T1.quantity', 'T2.name', 'T2.stock')
      ->join('items as T2', 'T1.code', '=', 'T2.code')
      ->where('T1.fk_id_purchase', $id)
      ->get();

      $docs = DB::table('docs')
      ->select('id','link')
      ->where('fk_id_purchase', $id)
      ->get();

      return response()->json(
        [
          'id' => $p->id,
          'fk_provider_id' => $p->fk_provider_id,
          'neto' => $p->neto,
          'description' => $p->description,
          'purchase_item' => $p_item,
          'docs' => $docs
        ]
        );
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
      $query = DB::table('purchases')
      ->where('fk_user_id', $this->userID)
      ->where('id', $id)
      ->update(
        [
          'fk_provider_id' => $request['provider'],
          'neto' => $request['neto'],
          'description' => $request['description']
        ]
       );

       if($request['code']) {
         $countItems = count($request['code']);
         if($countItems >= 1) {
           for ($i=0; $i < $countItems; $i++) {

              // AUMENTAR STOCK
              DB::table('items')
              ->where('fk_user_id', $this->userID)
              ->where('code', $request['code'][$i])
              ->increment('stock', $request['quantity'][$i]);

              // INSERTAR ITEMS
               DB::table('purchase_item')->insert(
                 [
                   'code' => $request['code'][$i],
                   'quantity' => $request['quantity'][$i],
                   'fk_id_purchase' => $id
                 ]
               );
            }
         }
       }

       $authEncrypt = $this->encrypt_decrypt('encrypt', $this->userID);

       if(!File::exists('docs/'.$authEncrypt)) {
          File::makeDirectory('docs/'.$authEncrypt, 0777, true, true);
        }

      if($request->file('files')){
        $uploadedFiles=$request->file('files');
        foreach ($uploadedFiles as $file){
          $file_name = md5(rand(0, 100000)+strtotime(time())).'.'.strtolower($file->getClientOriginalExtension());
          $file->move('docs/'.$authEncrypt, $file_name);
          DB::table('docs')->insert([
            'link' => 'docs/'.$authEncrypt.'/'.$file_name,
            'fk_id_purchase' => $id
          ]);
        }
      }

      return response()->json(['purchase' => true]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $purchase = DB::table('purchases')->where('id', $id)->delete();
        $purchase_item = DB::table('purchase_item')->where('fk_id_purchase', $id)->get();
        $docs = DB::table('docs')->where('fk_id_purchase', $id)->get();
        foreach ($purchase_item as $key => $value) {
            DB::table('items')
            ->where('fk_user_id', $this->userID)
            ->where('code', $value->code)
            ->decrement('stock', $value->quantity);
        }
        foreach ($docs as $key => $value) {
            File::delete($value->link);
        }
        $purchase_item = DB::table('purchase_item')->where('fk_id_purchase', $id)->delete();
        $docs = DB::table('docs')->where('fk_id_purchase', $id)->delete();
    }
}
