<?php
/**
 * Created by PhpStorm.
 * User: jupiter.k
 * Date: 2018/6/17
 * Time: 13:05
 */
return [

    /*
    |--------------------------------------------------------------------------
    | customized http code
    |--------------------------------------------------------------------------
    |
    | The first number is error type, the second and third number is
    | product type, and it is a specific error code from fourth to
    | sixth.But the success is different.
    |
    */

    'code' => [
        200 => '成功',
        200001 => '缺少必要的参数',

        401 => '登录失败',
        402 => '参数错误',
    ],
];