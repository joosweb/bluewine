<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class MallController extends Controller
{
    private $userID;
    private $best_seller_items_limit = 11;
    private $max_pagination_items = 12;
    

    public function index($mall_id)
    { // 
        try {
            $this->userID = $this->encrypt_decrypt('decrypt', $mall_id);
            $best_seller_items = $this->best_seller_items();
            $best_sellers_categories = $this->best_sellers_categories($best_seller_items);
            $user_categories = $this->user_categories();
            $user_data = $this->get_user_data();
            $user_config_page = $this->user_page_config();
            $user_all_items = $this->user_all_items();
            $mall_id = json_encode($mall_id);
            return view('shop.index', compact("mall_id", "user_all_items", "user_data", "best_seller_items", "best_sellers_categories", "user_categories", "user_config_page"));
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function pagination(Request $request) {

        $mall_id = $request->get('mall_id');
        $mall_user_id = $this->encrypt_decrypt('decrypt', $mall_id);

        $user_pagination_items = DB::table('items as T1')
            ->select('T1.id', 'T1.name', 'T1.code', 'T1.price', 'T1.photo', 'T1.sold', 'T1.stock', 'T2.name as category_name')
            ->join('categories as T2', 'T2.id', '=', 'T1.category_id')
            ->where('T1.fk_user_id', $mall_user_id)
            ->orderBy('sold', 'DESC')
            ->paginate($this->max_pagination_items);

        return response()->json($user_pagination_items);
    }
    

    public function user_all_items()
    {
        $user_all_items = DB::table('items as T1')
            ->select('T1.id', 'T1.name', 'T1.code', 'T1.price', 'T1.photo', 'T1.sold', 'T1.stock', 'T2.name as category_name')
            ->join('categories as T2', 'T2.id', '=', 'T1.category_id')
            ->where('T1.fk_user_id', $this->userID)->orderBy('sold', 'DESC')->paginate($this->max_pagination_items);
        return json_encode($user_all_items);
    }

    public function get_user_data()
    {
        return json_encode(DB::table('users')->where('id', $this->userID)->select('phone', 'email', 'address')->first());
    }

    public function best_sellers_categories($best_seller_items)
    {
        try {
            $categories_best_seller = [];
            foreach ($best_seller_items as $value) {
                array_push($categories_best_seller, $value->category_name);
            }
            return array_unique($categories_best_seller);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function best_seller_items()
    {
        try {
            $best_seller_items = DB::table('items as T1')
                ->select('T1.id', 'T1.name', 'T1.code', 'T1.price', 'T1.photo', 'T1.sold', 'T1.stock', 'T2.name as category_name')
                ->join('categories as T2', 'T2.id', '=', 'T1.category_id')
                ->where('T1.fk_user_id', $this->userID)->orderBy('sold', 'DESC')->limit($this->best_seller_items_limit)->get();
            return $best_seller_items;
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function user_categories()
    {
        try {
            $categories = DB::table('categories')->select('id', 'name')
                ->where('fk_user_id', $this->userID)->get();
            return json_decode($categories);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function user_page_config()
    {
        try {
            return DB::table('page_config')->select('name_ecommerce')->where('fk_user_id', $this->userID)->first();
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
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
}
