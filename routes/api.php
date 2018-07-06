<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Api Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Api routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your Api!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('login', 'Api\UsersController@login');
Route::post('register', 'Api\UsersController@store');
Route::post('sendSms', 'Api\UsersController@sendSms');
//Route::post('add', 'Api\AddressesController@add');    // 添加省市区

//    Goods
Route::get('categories/{category}', 'Api\GoodsController@getGoodsList');
Route::post('goods/search', 'Api\GoodsController@search');
Route::get('goods/{goods}', 'Api\GoodsController@getGoodsDetail');

Route::group(['middleware' => 'auth:api'], function () {
//    Carts
    Route::get('carts', 'Api\CartsController@index');
    Route::post('carts', 'Api\CartsController@store');
    Route::delete('carts', 'Api\CartsController@destroy');
    Route::get('carts/confirm', 'Api\CartsController@confirm');


//    Categories
    Route::get('categories', 'Api\CategoriesController@getFirstCategories');


//    Users
    Route::post('avatars', 'Api\UsersController@uploadAvatar');
    Route::put('info', 'Api\UsersController@update');

//    Addresses
    Route::get('provinces', 'Api\AddressesController@getProvinces');
    Route::get('cities/{district}', 'Api\AddressesController@getCities');
    Route::get('areas/{district}', 'Api\AddressesController@getAreas');
    Route::get('addresses', 'Api\AddressesController@index');
    Route::post('addresses', 'Api\AddressesController@store');
    Route::put('addresses/{address}', 'Api\AddressesController@update');

//    Coupons
    Route::get('coupons', 'Api\CouponsController@index');
    Route::post('coupons/{coupon}', 'Api\CouponsController@add');

//    Orders
    Route::get('orders', 'Api\OrdersController@index');
    Route::get('orders/{order}', 'Api\OrdersController@show');
    Route::post('orders', 'Api\OrdersController@store');
});

Route::get('orders/{order}/alipay', 'Api\OrdersController@alipay')->name('alipay');
Route::post('orders/alipay/notify', 'Api\OrdersController@alipayNotify')->name('alipay.notify');
Route::get('orders/alipay/return', 'Api\OrdersController@alipayReturn')->name('alipay.return');

