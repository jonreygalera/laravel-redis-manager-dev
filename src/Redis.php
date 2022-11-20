<?php

namespace Jonreyg\LaravelRedisManager;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Redis as RedisLaravel;
use Throwable;
use Exception;

class Redis extends RedisLaravel
{
   private static $proceed_when_down = false;
   private static $is_up = false;

   public static function proceedWhenDown()
   {
        self::$proceed_when_down = true;
   }

   public static function checkRedisConnection() 
   {

        try {
            self::connection()->ping();
            self::$is_up = true;
        } catch (Throwable $e) {
            if(!static::$proceed_when_down) throw new Exception($e->getMessage());
           self::$is_up = false;
        }
        
   }

   public static function isUp()
   {
        return self::$is_up;
   }

   public static function canProceedWhenDown()
   {
       return self::$proceed_when_down;
   }

   public static function canProceedOnDown()
   {
        return (self::isUp() === false) && (self::canProceedWhenDown());
   }
}