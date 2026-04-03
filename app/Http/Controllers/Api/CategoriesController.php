<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;

class CategoriesController extends PlansController
{
    private $userID;
    private $categoriesMAX;

    public function __construct(){
      $this->middleware('auth');
      $this->middleware(function ($request, $next) {
      $plan = new PlansController(); // getplanlimit instance
      $this->categoriesMAX = $plan->getLimitplan(Auth::user()->fk_id_plan, 'cat_max');
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
      $query = DB::table('categories as T1')
        ->select('T1.id','T1.name', 'T1.created_at','T1.updated_at')
        ->selectRaw('count(T2.id) count')
        ->leftJoin('items as T2', 'T1.id', '=', 'T2.category_id')
        ->where('T1.fk_user_id', $this->userID)
        ->groupBy('T1.id','T1.name', 'T1.created_at','T1.updated_at')
        ->paginate(8);

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

    public function store(Request $request)
    {
        //
        $cats = DB::table('categories')->where('fk_user_id', $this->userID)->count();
        if($this->categoriesMAX == 0 || $cats < $this->categoriesMAX){
          $query = DB::table('categories')
          ->select('id')
          ->where('fk_user_id', $this->userID)
          ->where('name', $request['name'])->count();

          if($query) {
            throw  \Illuminate\Validation\ValidationException::withMessages([
             'name' => 'El nombre de la categoria ya existe.'
           ]);
          }

          $validator = $this->validate($request,[
              'name' => 'required|string|max:50'
            ],
            [
              'name.required' => 'El campo nombre de la categoria es obligatorio..'
            ]
          );

          $nameCategory = strtoupper($request['name']);

          $query = DB::table('categories')->insert(['name' => $nameCategory, 'fk_user_id' => $this->userID]);

          return response()->json(['success' => true]);
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
     public function show($value) {
       $query = DB::table('categories as T1')
         ->select('T1.id','T1.name', 'T1.created_at','T1.updated_at')
         ->selectRaw('count(T2.id) count')
         ->leftJoin('items as T2', 'T1.id', '=', 'T2.category_id')
         ->groupBy('T1.id','T1.name', 'T1.created_at','T1.updated_at')
         ->where('T1.fk_user_id', $this->userID)
         ->where('T1.name', 'LIKE', '%' . $value . '%')
         ->paginate(8);

         return $query;
     }

     public function selectCategories(){
       $query = DB::table('categories')
       ->select('id', 'name')
       ->where('fk_user_id', $this->userID)
       ->get();
       return $query;
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
        $query = DB::table('categories')
        ->select('id')
        ->where('id', '!=', $id)
        ->where('fk_user_id', $this->userID)
        ->where('name', $request['name'])->count();

        if($query) {
          throw  \Illuminate\Validation\ValidationException::withMessages([
           'name' => 'El nombre de la categoria ya existe.'
         ]);
        }

        $validator = $this->validate($request,[
            'name' => 'required|string|max:50'
          ],
          [
            'name.required' => 'El campo nombre de la categoria es obligatorio..'
          ]
        );

        $nameCategory = strtoupper($request['name']);

        $query = DB::table('categories')
        ->where('id', $id)
        ->update(['name' => $nameCategory,'updated_at' => date("Y-m-d H:i:s")]);

        return response()->json(['success' => 'OK'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function destroy($id){
       $delete = DB::table('categories')
       ->where('fk_user_id', $this->userID)
       ->where('id', $id)->delete();
       return response()->json('success', 200);
     }
}
