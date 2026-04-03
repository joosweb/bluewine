<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;

class CriticalItemsController extends Controller
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
        $query = DB::table('critical_items')->where('fk_user_id', $this->userID)
        ->paginate(8);
        return $query;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkExist($code)
    {
        $query = DB::table('critical_items')->where('fk_user_id', $this->userID)
        ->where('code', $code)->count();
        if($query){
          return true;
        }
        else
        {
          return false;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $items = [];
        if($request['code']){
           $countItems = count($request['code']);
           if($countItems >= 1) {
             for ($i=0; $i < $countItems; $i++) {
               if(!$this->checkExist($request['code'][$i])){
                 DB::table('critical_items')->insert(
                   [
                     'fk_user_id' => $this->userID,
                     'code' => $request['code'][$i],
                     'days' => $request['days'][$i]
                   ]
                 );
               }
               else {
                 array_push($items, $request['code'][$i]);
               }
             }
           }
        }
        if(empty($items)) {
          return response()->json(['save' => true]);
        }
        else {
          return response()->json(
            [
              'save' => true,
              'items' => $items
            ]
          );
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
        //
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
    public function update(Request $request)
    {
          DB::table('critical_items')
          ->where('fk_user_id', $this->userID)
          ->where('code', $request['code'])
          ->update(
            [
              'fk_user_id' => $this->userID,
              'code' => $request['code'],
              'days' => $request['days']
            ]
          );

          return response()->json(['save' => true]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($code)
    {
        $query = DB::table('critical_items')
        ->where('fk_user_id', $this->userID)
        ->where('code', $code)
        ->delete();
        return response()->json(['delete' => true]);
    }
}
