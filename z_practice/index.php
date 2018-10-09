<?php
/**
 * Created by PhpStorm.
 * User: jupiterk
 * Date: 2018/9/28
 * Time: 22:49
 */

//单例模式
class singleton
{
    private static $instance;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function getInstance()
    {
        if (self::$instance instanceof singleton) {
            return self::$instance;
        } else {
            return self::$instance = new singleton();
        }
    }
}


class factory
{
    public static function getSingletonInstance(){
        return singleton::getInstance();
    }
}

var_dump(factory::getSingletonInstance());
var_dump(factory::getSingletonInstance());
var_dump(singleton::getInstance());
exit;