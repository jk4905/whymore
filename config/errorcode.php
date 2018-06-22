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
        20001 => '缺少必要的参数',

        40001 => '登录失败',
        40002 => '参数错误',
        40003 => '发送验证码失败',
        40004 => '验证码不正确',
        40005 => '图片上传失败',
    ],
];