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

        40014 => '登录失败',
        40002 => '参数错误',
        40003 => '发送验证码失败',
        40004 => '验证码不正确',
        40005 => '图片上传失败',
        40007 => '优惠券无效',
        40008 => '购物车中没有这些商品',
        40009 => '没有此分类',
        40010 => '订单金额不正确',
        40011 => '支付配置不存在',
        40012 => 'app_id 不正确',
        40013 => '订单状态错误',

        40301 => '您没有此权限',
        40302 => '您还未登录',

        40020 => '此反馈已被删除',
    ],
];