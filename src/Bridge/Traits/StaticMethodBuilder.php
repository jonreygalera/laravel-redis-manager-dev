<?php

namespace Jonreyg\LaravelRedisManager\Bridge\Traits;

use Illuminate\Support\Facades\Redis;
use Jonreyg\LaravelRedisManager\Bridge\Keywords;

trait StaticMethodBuilder
{
    private static $static_method = '';

    public static function staticBuilder()
    {
        $class = get_called_class();
        $object = new $class;
        switch(self::$static_method) {
            case Keywords::INSERT: return $object->insertQuery(...func_get_args());
            case Keywords::MULTISERT: return $object->multisertQuery(...func_get_args());
        }
    }

    public static function __callStatic($name, $arguments)
    {
        self::$static_method = $name;
        call_user_func(array(get_called_class(), 'staticBuilder'), ...$arguments);
    }
}