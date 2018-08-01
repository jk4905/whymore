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
    $router->get('api/admins', 'ApiAdminUsersController@admins');
    $router->get('api/robot_conf/{robot_configuration}', 'ApiAdminUsersController@downloadRobotConfiguration');
    $router->resource('banners', BannersController::class);
    $router->resource('categories', CategoriesController::class);
    $router->resource('goods', GoodsController::class);
    $router->resource('orders', OrdersController::class);
    $router->resource('streamers', StreamersController::class);
    $router->resource('coupons', CouponsController::class);
    $router->resource('robot_configuration', RobotConfigurationController::class);
});
