<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/test', 'HomeController@generarDocumento');


Route::get('/', function () {
    return view('welcome');
})->middleware('auth');

Route::get('/gett', 'Api\SalesController@total_status_informesii');

Auth::routes();

Route::get('/xml-to-pdf', 'Api\XmlController@index');

// SHOP RECORD

Route::post('save-shop/{commit}', 'Api\ShoppingCartController@SaveShop');
Route::get('get-shop', 'Api\ShoppingCartController@show');
Route::post('return-shop/{token}', 'Api\ShoppingCartController@returnShop');
Route::post('delete-shop/{token}', 'Api\ShoppingCartController@deleteShop');
// AUTH SOCIAL

Route::get('auth/{provider}', 'Auth\GoogleController@redirectToProvider')->name('social.auth');
Route::get('auth/{provider}/callback', 'Auth\GoogleController@handleProviderCallback');

// NOTIFICATIONS
Route::get('/notifications/{key}', 'Api\NotificationsController@notifications');
Route::post('/getCountNotifications', 'Api\NotificationsController@countnotifications');
Route::post('/getNotifications', 'Api\NotificationsController@getnotifications');
Route::post('/getPhotoProfile', 'Api\NotificationsController@getPhotoProfile');
// LOGOUT
Route::get('/logout', '\App\Http\Controllers\Auth\LoginController@logout');

// CRITICAL ITEMS
Route::post('/critical_items', 'Api\CriticalItemsController@store');
Route::post('/critical_items_get', 'Api\CriticalItemsController@index');
Route::post('/critical_items/update', 'Api\CriticalItemsController@update');
Route::delete('/critical_items/{code}', 'Api\CriticalItemsController@destroy');

// PROFILE CONFIG
Route::post('/profile_config_save', 'Api\UserController@store');
Route::post('/profile_config_get', 'Api\UserController@index');
// GENERAL CONFIG
Route::post('/general_config_save', 'Api\PageConfig@store');
Route::post('/general_config_get', 'Api\PageConfig@index');
Route::post('/general_config_get_filter', 'Api\PageConfig@show');
Route::post('/general_config_name_pos', 'Api\PageConfig@name');
Route::post('/general_config_critical_stock', 'Api\PageConfig@criticalStock');
Route::post('/general_config_critical_get', 'Api\PageConfig@getConfigStock');
// BSALE CONFIG
Route::post('/bsale_config_save', 'Api\BsaleConfig@store');
Route::post('/bsale_config_get', 'Api\BsaleConfig@index');

// PURCHASES
Route::get('/items/autocomplete', "Api\PurchaseController@autocomplete");
Route::post('/check/code/{code}', "Api\PurchaseController@checkcode");
Route::post('purchases/search/provider/{run}', "Api\PurchaseController@search");
Route::post('/purchases', "Api\PurchaseController@store");
Route::post('/purchases/get', "Api\PurchaseController@index");
Route::post('/purchases/get/providers', "Api\PurchaseController@providers");
Route::post('/purchases/details/{pruchaseID}', "Api\PurchaseController@show");
Route::post('/purchases/edit/{pruchaseID}', "Api\PurchaseController@edit");
Route::put('/purchases/update/{pruchaseID}', "Api\PurchaseController@update");
Route::delete('/purchases/delete/{purchaseID}', "Api\PurchaseController@destroy");
Route::delete('/purchase/item/delete/{id}', "Api\PurchaseController@itemdestroy");
Route::delete('/purchase/doc/delete/{id}', "Api\PurchaseController@docdestroy");
Route::post('/total_purchases', "Api\PurchaseController@totalpurchases");

// SII CONSULTA DE RUTº
Route::post('/sii/{rut}', 'Api\SiiController@dataCompany');

// GENERAR DOCUMENTOS
Route::post('/invoice/{TotalPay}/{sendMail}/{PayType}/{discount}/{idCliente}/{cotizacion}', 'Api\BsaleController@GenerarFactura');
Route::post('/ticket/{TotalPay}/{PayType}/{discount}/{idCliente}', 'Api\BsaleController@GenerarBoleta');
Route::post('/folios/{code}', 'Api\BsaleController@FoliosAvailables');
// CHECK AUTH
Route::get('/check-auth', 'Api\CheckAuthController@check');

// HOME
Route::post('/counters', 'Api\HomeController@Counters');
Route::post('/chart-day', 'Api\HomeController@ChartOfDay');
Route::post('/best-sellers', 'Api\HomeController@BestSellers');
Route::post('/rank', 'Api\HomeController@ItemsLessStock');
Route::post('/documents', 'Api\HomeController@LastDocuments');
Route::post('/sales_of_day', 'Api\HomeController@reportsales');

// CLIENTS ROUTES
Route::post('/update/company/{id}','Api\ClientsController@updateCompany');
Route::post('/get/clients', "Api\ClientsController@index");
Route::get('/clients/autocomplete', "Api\ClientsController@autocomplete");
Route::post('/clients/search/{id}', "Api\ClientsController@SearchClient");
Route::post('/get/clients/{filter}/{value}', "Api\ClientsController@show");
Route::delete('/delete/clients/{id}', "Api\ClientsController@destroy");
Route::put('/update/clients/{id}', "Api\ClientsController@update");
Route::post('/post/clients', "Api\ClientsController@store");

// PROVIDERS ROUTES
Route::post('/get/providers', "Api\ProvidersController@index");
Route::get('/providers/autocomplete', "Api\ProvidersController@autocomplete");
Route::get('/providers/search/{id}', "Api\ProvidersController@SearchClient");
Route::post('/get/providers/{filter}/{value}', "Api\ProvidersController@show");
Route::delete('/delete/providers/{id}', "Api\ProvidersController@destroy");
Route::put('/update/providers/{id}', "Api\ProvidersController@update");
Route::post('/post/providers', "Api\ProvidersController@store");

// SELLERS
Route::post('/get_sellers', 'Api\SellersController@index');
Route::post('/set_sellers', 'Api\SellersController@store');
Route::post('/update_sellers/{id}', 'Api\SellersController@update');
Route::delete('/delete_seller/{id}', 'Api\SellersController@destroy');
Route::post('/get_sales/{id}', 'Api\SellersController@getsales');
Route::post('/get_seller/{run}', 'Api\SellersController@show');

// plans
Route::post('/get/plan', 'Api\PlansController@index');

// sales
Route::post('/sales', 'Api\SalesController@store');
Route::post('/sales/get', 'Api\SalesController@index');
Route::post('/sales/get/filters', 'Api\SalesController@show');
Route::post('/sales/details/{id}', 'Api\SalesController@saleDetails');
//Route::post('/sales/items', 'Api\SalesController@items');
Route::delete('/sales/delete/{id}', 'Api\SalesController@destroy');

Route::post('/items-to-sale', 'Api\SalesController@inserItemsToSale');


// expenses
Route::post('/new-expense', 'Api\ExpensesController@store');
Route::post('/udpate-expense/{id}', 'Api\ExpensesController@update');
Route::post('/expenses', 'Api\ExpensesController@index');
Route::delete('/expenses/{id}/delete', 'Api\ExpensesController@destroy');
Route::post('/expenses/search', 'Api\ExpensesController@search');
Route::get('/excel/{filter}/{text}/{from}/{to}', 'Api\ExpensesController@excel');

// point of sale
Route::post('/pos/{category}/{filter}/{text}', 'Api\PosController@items');
Route::post('/pos/{filter}/{text}', 'Api\PosController@itemCategoryF');
Route::post('/pos/{code}', 'Api\PosController@GetItemForCode');
Route::post('/update/stock/{id}/{quantity}/{operation}', 'Api\PosController@IncrementDecrementStock');
Route::post('/set/stock/{id}/{quantity}', 'Api\PosController@setstock');
Route::post('/stock/{code}', 'Api\PosController@stock');
Route::get('/get/users/shop/{id}', 'Api\UserController@getUsersShop');
Route::get('/change/session/{id}/password/{password}', 'Api\UserController@changeSession');

// items
Route::post('/item', 'Api\ItemsController@index');
Route::get('/item/{filter}/{value}', 'Api\ItemsController@show');
Route::post('/items/get/', 'Api\ItemsController@items');
Route::post('/items/post', 'Api\ItemsController@store');
Route::put('/update/item/{id}', "Api\ItemsController@update");
Route::delete('/items/delete/{id}', 'Api\ItemsController@destroy');
Route::post('/get_sales_id/{id}', 'Api\ItemsController@get_sales');

// categories
Route::post('/select/categories', 'Api\CategoriesController@selectCategories');
//Route::post('/total/items/category/{id}', 'Api\CategoriesController@TotalItems');
Route::post('/categories/get', 'Api\CategoriesController@index');
Route::post('/categories', 'Api\CategoriesController@store');
Route::put('/categories/{id}', "Api\CategoriesController@update");
Route::post('/categories/{value}', 'Api\CategoriesController@show');
Route::delete('/categories/{id}', 'Api\CategoriesController@destroy');

// MALL SHOP

Route::get('/mall/{id}', 'Api\MallController@index');
Route::get('/mall/pagination/items', 'Api\MallController@pagination');

// Daily Box

Route::resource('/daily', 'Api\DailyBoxController');
Route::post('/get/boxs', 'Api\DailyBoxController@getBoxs');
Route::post('/show/box/{id}', 'Api\DailyBoxController@showBox');

Route::get('/daily-summary-turn', 'Api\DailyBoxController@dailySummaryTurn');
Route::get('/daily-close-turn', 'Api\DailyBoxController@closeTurn');

// REPORTS
Route::get('/reports/for/seller', 'Api\ReportsController@index');

// USERS 
Route::get('/users', 'Api\UserController@listUsers');

//  GENERAR VOUCHER INTERNO

Route::get("/generar-voucher-interno/{id}", "Api\VoucherController@remota");
Route::get("/generar-voucher-local/{id}", "Api\VoucherController@local");

Route::get('/{vue_capture?}', function () {
    return view('welcome');
 })->where('vue_capture', '^(?!storage).*$')->middleware('auth');



