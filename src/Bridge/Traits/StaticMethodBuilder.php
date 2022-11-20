<?php

namespace Jonreyg\LaravelRedisManager\Bridge\Traits;

use Jonreyg\LaravelRedisManager\Redis;
use Jonreyg\LaravelRedisManager\Bridge\Keywords;

trait StaticMethodBuilder
{
    private static $static_method = '';
    private static $class = '';

    public static function staticBuilder()
    {
        $object = new self::$class;
        
        switch(self::$static_method) {
            // Query
            case Keywords::INSERT: return $object->insertQuery(...func_get_args());
            case Keywords::ALL: return $object->allQuery(...func_get_args());

            // Expiration
            case Keywords::ADDEXPIRATION: return $object->addExpirationCommand(...func_get_args());
            case Keywords::EXPIREHOURS: return $object->expireHoursCommand(...func_get_args());
            case Keywords::EXPIREHOUR: return $object->expireHourCommand(...func_get_args());
            case Keywords::EXPIREHALFHOUR: return $object->expireHalfHourCommand(...func_get_args());
            case Keywords::EXPIREDAYS: return $object->expireDaysCommand(...func_get_args());
            case Keywords::EXPIREDAY: return $object->expireDayCommand(...func_get_args());
            case Keywords::EXPIREWEEK: return $object->expireWeekCommand(...func_get_args());
            case Keywords::EXPIREWEEKS: return $object->expireWeeksCommand(...func_get_args());
            case Keywords::EXPIREMINUTES: return $object->expireMinutesCommand(...func_get_args());
            case Keywords::EXPIREMINUTE: return $object->expireMinuteCommand(...func_get_args());
            case Keywords::EXPIREYEARS: return $object->expireYearsCommand(...func_get_args());
            case Keywords::EXPIREYEAR: return $object->expireYearCommand(...func_get_args());
        }
    }

    public static function __callStatic($name, $arguments)
    {
        self::$class = get_called_class();
        self::$static_method = $name;
        return call_user_func(array(self::$class, 'staticBuilder'), ...$arguments);
    }
}