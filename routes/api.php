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
    Route::post('getDetails', 'API\ApiController@getDetails');
    Route::post('getGoods', 'API\GoodsController@show');
    Route::get('test', 'API\CartsController@test');
    Route::get('myCart', 'API\CartsController@index');
    Route::post('addGoods', 'API\CartsController@store');
    Route::post('delGoods', 'API\CartsController@destroy');
    Route::post('getFirstCategories', 'API\CategoriesController@getFirstCategories');
    Route::get('getGoodsList/{category}', 'API\GoodsController@getGoodsList');
    Route::get('getGoodsDetail/{goods}', 'API\GoodsController@getGoodsDetail');
    Route::post('uploadAvatar', 'API\UsersController@uploadAvatar');
    Route::post('updateInfo', 'API\UsersController@update');
});