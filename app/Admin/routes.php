<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {
    $router->get('/', 'HomeController@index');
    $router->get('users', 'UsersController@index');
    $router->resource('banners', BannersController::class);
    $router->resource('categories', CategoriesController::class);
    $router->resource('goods', GoodsController::class);
    $router->resource('orders', OrdersController::class);
    $router->resource('streamers', StreamersController::class);
});
