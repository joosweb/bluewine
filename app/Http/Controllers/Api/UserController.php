<?php

namespace App\Http\Controllers\Api;

use Intervention\Image\Exception\NotReadableException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
Use Image;
use File;
use DB;
use Auth;
use App\User;
use Hash;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $query = DB::table('users')
        ->select('name','last_name','photo','email', 'phone', 'address','city','fk_id_user_type','created_at','shop')
        ->where('id', Auth::id())->first();
        $query->shop_url = $this->encrypt_decrypt('encrypt', $query->shop);

        return response()->json($query);
    }

    public function listUsers() {
      $query = DB::table('users')
      ->select('id','name')
      ->where('status', 1)->get();
      return $query;
    }

    public function getUsersShop($shop_id) {
      $query = DB::table('users')
      ->select('id','name','last_name','photo','email')
      ->where('status', 1)
      ->where('shop', $shop_id)->get();
      return $query;
    }

    public function changeSession($id, $password) {
        $user = User::find($id);
        if (Hash::check($password, $user->password)) {
            Auth::logout();
            Auth::loginUsingId($id, true);
            return response()->json(['status' => true]);
        }
        else {
          return response()->json(['status' => false]);
        }
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
      try {
        if($request->photo){
          $find = DB::table('users')->select('photo')->where('id', Auth::id())->first();
          if($find->photo){
          File::delete($find->photo);
          }

          $name = md5(rand(0, 100000)+strtotime(time())).'.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
          $thumbnailImage = Image::make($request->photo);
          $photoResize = $thumbnailImage->resize(200, 200);
          $folderUser = $this->encrypt_decrypt('encrypt', Auth::id());
          if(!File::exists('docs/'.$folderUser.'/avatar/')) {
             File::makeDirectory('docs/'.$folderUser.'/avatar/', 0777, true, true);
          }
          $thumbnailPath = "docs/".$folderUser."/avatar/";
          $photoResize->save($thumbnailPath.$name, 100);
          DB::table('users')->where('id', Auth::id())->update(['photo' => $thumbnailPath.$name]);
        }

        if($request['password']) {
          $query = DB::table('users')->where('id', Auth::id())->update(
            [
              'name' => $request['name'],
              'last_name' => $request['last_name'],
              'email' => $request['email'],
              'password' => bcrypt($request['password']),
              'phone' => $request['phone'],
              'city' => $request['city'],
              'address' => $request['address'],
              'facebook_link' => $request['facebook_link'],
              'company_number' => $request['company_number']
            ]
          );
        }
        else {
          $query = DB::table('users')->where('id', Auth::id())->update(
            [
              'name' => $request['name'],
              'last_name' => $request['last_name'],
              'email' => $request['email'],
              'phone' => $request['phone'],
              'city' => $request['city'],
              'address' => $request['address'],
              'facebook_link' => $request['facebook_link'],
              'company_number' => $request['company_number']
            ]
          );
        }
        return response()->json(['update' => true]);
      } catch (\Exception $e) {
        return response()->json(['error' => $e]);
      }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $query = DB::table('users')
      ->select('email','phone','photo')
      ->where('id', $id)->first();

      return response()->json(['email' => $query->email, 'phone' => $query->phone, 'photo' => $query->photo]);
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
