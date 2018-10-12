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

Route::post('login', 'Api\UsersController@login');
Route::post('register', 'Api\UsersController@store');
Route::post('sendSms', 'Api\UsersController@sendSms');
Route::get('user/logout', 'Api\UsersController@logout');
//Route::post('add', 'Api\AddressesController@add');    // 添加省市区

//    Goods
Route::get('goods', 'Api\GoodsController@index');
Route::post('goods/search', 'Api\GoodsController@search');
Route::get('goods/{goods}', 'Api\GoodsController@getGoodsDetail');

//    Categories
Route::get('categories', 'Api\CategoriesController@getFirstCategories');
Route::get('categories/{category}', 'Api\CategoriesController@getGoodsList');

//    banners
Route::get('banners', 'Api\BannersController@index');

Route::middleware(['auth:api', \Barryvdh\Cors\HandleCors::class])->group(function () {
//    Carts
    Route::get('carts', 'Api\CartsController@index');
    Route::post('carts', 'Api\CartsController@store');
    Route::put('carts', 'Api\CartsController@update');
    Route::delete('carts', 'Api\CartsController@destroy');
    Route::get('carts/confirm', 'Api\CartsController@confirm');

//    Users
    Route::get('user', 'Api\UsersController@index');
    Route::post('user/upload', 'Api\UsersController@uploadAvatar');
    Route::put('user', 'Api\UsersController@update');

//    Addresses
    Route::get('provinces', 'Api\AddressesController@getProvinces');
    Route::get('cities/{district}', 'Api\AddressesController@getCities');
    Route::get('areas/{district}', 'Api\AddressesController@getAreas');
    Route::get('addresses', 'Api\AddressesController@index');
    Route::get('addresses/{address}', 'Api\AddressesController@view');
    Route::post('addresses', 'Api\AddressesController@store');
    Route::put('addresses/{address}', 'Api\AddressesController@update');
    Route::delete('addresses/{address}', 'Api\AddressesController@destroy');

//    Coupons
    Route::get('coupons', 'Api\CouponsController@index');
    Route::get('coupons/usable', 'Api\CartsController@getUsableCoupon');

    Route::post('coupons/{coupon}', 'Api\CouponsController@add');

//    Orders
    Route::get('orders', 'Api\OrdersController@index');
    Route::get('order/freight', 'Api\CartsController@getFreight');
    Route::get('orders/{order}', 'Api\OrdersController@show');
    Route::post('orders', 'Api\OrdersController@store');
});

Route::post('orders/alipay/notify', 'Api\OrdersController@alipayNotify')->name('alipay.notify');
Route::get('orders/alipay/return', 'Api\OrdersController@alipayReturn')->name('alipay.return');
Route::get('orders/{order}/alipay', 'Api\OrdersController@alipay')->name('alipay');

//    Feedback
Route::get('feedbacks', 'Api\FeedbacksController@index');
Route::post('feedbacks', 'Api\FeedbacksController@store');
Route::get('feedbacks/{feedback}', 'Api\FeedbacksController@show');


//    newRobot
Route::get('robot/config', 'Api\RobotsController@getConfig');
Route::post('robot/message', 'Api\RobotsController@store');
Route::post('robot/upload', 'Api\RobotsController@upload');
