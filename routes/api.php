<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('login', 'API\UsersController@login');
Route::post('register', 'API\UsersController@store');
Route::post('sendSms', 'API\UsersController@sendSms');


Route::group(['middleware' => 'auth:api'], function () {
//    Carts
    Route::get('goods', 'API\CartsController@index');
    Route::post('goods', 'API\CartsController@store');
    Route::delete('goods', 'API\CartsController@destroy');

//    Categories
    Route::get('categories', 'API\CategoriesController@getFirstCategories');

//    Goods
    Route::get('categories/{category}', 'API\GoodsController@getGoodsList');
    Route::get('goods/{goods}', 'API\GoodsController@getGoodsDetail');

//    Users
    Route::post('avatars', 'API\UsersController@uploadAvatar');
    Route::put('info', 'API\UsersController@update');

//    Addresses
    Route::get('provinces', 'API\AddressesController@getProvinces');
    Route::get('cities/{city}', 'API\AddressesController@getCities');
    Route::get('areas/{area}', 'API\AddressesController@getAreas');
    Route::get('addresses', 'API\AddressesController@index');
    Route::post('addresses', 'API\AddressesController@store');
    Route::put('addresses/{address}', 'API\AddressesController@update');

//    Coupon
    Route::get('coupons', 'API\CouponsController@index');
    Route::post('coupons/{coupon}', 'API\CouponsController@add');

});