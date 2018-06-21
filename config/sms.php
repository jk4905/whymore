<?php
/**
 * Created by PhpStorm.
 * User: jupiter.k
 * Date: 2018/6/21
 * Time: 16:05
 */
return [
    // HTTP 请求的超时时间（秒）
    'timeout' => 5.0,

    // 默认发送配置
    'default' => [
        // 网关调用策略，默认：顺序调用
        'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

        // 默认可用的发送网关
        'gateways' => [
            'aliyun','alidayu'
        ],
    ],
    // 可用的网关配置
    'gateways' => [
        'errorlog' => [
            'file' => '/tmp/easy-sms.log',
        ],
        'aliyun' => [
            'access_key_id' => 'LTAILFhiSpJUo6W8',
            'access_key_secret' => 'sJGzGvA9YXoQTV95rKqBPGN9L1EfhW',
//            'app_key' => 'LTAIpHkHmCC8lJSV',
//            'app_secret' => 'izBy9zOrNjpA6djVv8dZ98cEc20vnK',
            'sign_name' => '知书',
        ],
//        'alidayu' => [
//            'app_key' => 'LTAIpHkHmCC8lJSV',
//            'app_secret' => 'izBy9zOrNjpA6djVv8dZ98cEc20vnK',
//            'sign_name' => '知书',
//        ],
    ],
];