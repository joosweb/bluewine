<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
Use Image;
use Intervention\Image\Exception\NotReadableException;
use File;
use Auth;
use DB;

class ItemsController extends Controller
{
    private $userID;
    private $itemsMAX;

    public function __construct(){
      $this->middleware('auth');
      $this->middleware(function ($request, $next) {
      $plan = new PlansController(); // getplanlimit instance
      $this->itemsMAX = $plan->getLimitplan(Auth::user()->fk_id_plan, 'items_max');
      if(Auth::user()->fk_id_user_type == 1)
      {
        $this->userID = Auth::id(); // IS ADMIN
      }
      else { // SELLER
        $query = DB::table('shop')
        ->select('fk_user_id_admin')
        ->where('id', Auth::user()->shop)
        ->first();
        $this->userID = $query->fk_user_id_admin;
      }
      return $next($request);
      });
    }

    public function get_sales($item_id) {
      $query = DB::table('items')->select('code')
      ->where('id', $item_id)->first();
      $count = DB::table('sales_item')
      ->select('fk_sales_id')
      ->where('code', $query->code)->count();
      return $count;
    }

    public function items($category = false, $filter = false, $texto = false) {
      if($category && $filter && $texto) {
        $query = DB::table('items as t1')
        ->select('t2.id as cn_id','t2.name as cn','t1.*')
        ->join('categories as t2', 't1.category_id', '=', 't2.id')
        ->where('t2.fk_user_id', $this->userID)
        ->where($filter, 'LIKE', '%' . $texto . '%')
        ->paginate(12);
        return $query;
      }
      else {
        $query = DB::table('items as t1')
        ->select('t2.id as cn_id','t2.name as cn','t1.*')
        ->join('categories as t2', 't1.category_id', '=', 't2.id')
        ->where('t2.fk_user_id', $this->userID)
        ->paginate(12);
        return $query;
      }
    }

    public function index()
    {
        //
        $query = DB::table('items as t1')
        ->select('t2.id as cn_id','t2.name as cn','t1.*')
        ->join('categories as t2', 't1.category_id', '=', 't2.id')
        ->where('t2.fk_user_id', $this->userID)
        ->paginate(5);

        return $query;
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

    public function store(Request $request)
    {
        $items = DB::table('items')->where('fk_user_id', $this->userID)->count();
        if($this->itemsMAX == 0 || $items < $this->itemsMAX){
          $validator = $this->validate($request,[
              'code' => 'required|unique:items,code',
              'name' => 'required',
              'category_id' => 'required',
              'stock' => 'required',
            ]
          );
          $authEncrypt = $this->encrypt_decrypt('encrypt', $this->userID);
          if(!File::exists('img/items/'.$authEncrypt)) {
             File::makeDirectory('img/items/'.$authEncrypt, 0777, true, true);
          }
          if($request->photo){
              $name = md5(rand(0, 100000)+strtotime(time())).'.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
              $thumbnailImage = Image::make($request->photo);
              $photoResize = $thumbnailImage->resize(1024, 768);
              $thumbnailPath = "img/items/".$authEncrypt."/";
              //\Image::make($request->photo)->save(public_path('img/items/').$name);
              $photoResize->save($thumbnailPath.$name, 100);
          }
          if(empty($name)) {
            $photo = '';
          }
          else {
            $photo = @$thumbnailPath.$name;
          }
          $query = DB::table('items')->insert(
            [
              'fk_user_id' => $this->userID,
              'code' => $request['code'],
              'name' => $request['name'],
              'category_id' => $request['category_id'],
              'price' => $request['price'],
              'purchase_price' => $request['purchase_price'],
              'type_price' => $request['type_price'],
              'photo' => $photo,
              'stock' => $request['stock'],
            ]
          );
          //$item = DB::table('items')->where('id', $query)->get();
          return response()->json(['success' => true], 200);
        }
        else {
          return response()->json(['success' => false], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($filter, $value)
    {
        if($filter == 'id') {
          $query = DB::table('items as t1')
          ->select('t2.id as cn_id','t2.name as cn','t1.*')
          ->join('categories as t2', 't1.category_id', '=', 't2.id')
          ->where('t2.fk_user_id', $this->userID)
          ->where('t1.id', $value)
          ->get();

          return $query;
        }
        else if($filter == 'category') {
          if($value == 'all'){
            $query = DB::table('items as t1')
            ->select('t2.id as cn_id','t2.name as cn','t1.*')
            ->join('categories as t2', 't1.category_id', '=', 't2.id')
            ->where('t2.fk_user_id', $this->userID)
            ->paginate(12);
            return $query;
          } else {
            $query = DB::table('items as t1')
            ->select('t2.id as cn_id','t2.name as cn','t1.*')
            ->join('categories as t2', 't1.category_id', '=', 't2.id')
            ->where('t2.fk_user_id', $this->userID)
            ->where('t1.category_id', $value)
            ->paginate(12);
            return $query;
          }
        }
        else {
          $query = DB::table('items as t1')
          ->select('t2.id as cn_id','t2.name as cn','t1.*')
          ->join('categories as t2', 't1.category_id', '=', 't2.id')
          ->where('t2.fk_user_id', $this->userID)
          ->where("t1.".$filter, 'LIKE', '%'.$value.'%')
          ->paginate(12);
          return $query;
        }
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
        $validator = $this->validate($request,[
            'code' => 'required|unique:items,code,'.$id,
            'name' => 'required',
            'category_id' => 'required',
            'stock' => 'required',
            ]
          );

          try {
            if($request->photo){
                $name = md5(rand(0, 100000)+strtotime(time())).'.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
                $thumbnailImage = Image::make($request->photo);
                $photoResize = $thumbnailImage->resize(200, 200);
                $thumbnailPath = "img/items/";
                $photoResize->save($thumbnailPath.$name, 100);
            }
          } catch (\Exception $e) {

          }

        if(empty($name)) {
          $query = DB::table('items')
            ->where('fk_user_id', $this->userID)
            ->where('id', $id)
            ->update(
            [
              'code' => $request['code'],
              'name' => $request['name'],
              'category_id' => $request['category_id'],
              'price' => $request['price'],
              'purchase_price' => $request['purchase_price'],
              'type_price' => $request['type_price'],
              'stock' => $request['stock'],
            ]
          );
        }
        else {
          // eliminar antigua imagen antes de guardar nueva
          $img_old = DB::table('items')
          ->select('photo')
          ->where('fk_user_id', $this->userID)
          ->where('id', $id)
          ->first();

          File::delete($img_old->photo);

          $query = DB::table('items')
            ->where('id', $id)
            ->update(
            [
              'code' => $request['code'],
              'name' => $request['name'],
              'category_id' => $request['category_id'],
              'price' => $request['price'],
              'purchase_price' => $request['purchase_price'],
              'photo' => $thumbnailPath.$name,
              'stock' => $request['stock'],
            ]
          );
        }

        return response()->json(['success' => 'OK'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function destroy($id){
       $delete = DB::table('items')
       ->where('fk_user_id', $this->userID)
       ->where('id', $id)->delete();
       return response()->json('success', 200);
     }
}