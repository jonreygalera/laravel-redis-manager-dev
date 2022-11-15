<?php

namespace Jonreyg\LaravelRedisManager\Bridge\Traits;

use Illuminate\Support\Facades\Redis;
use Jonreyg\LaravelRedisManager\Bridge\Keywords;

trait NonStaticMethodBuilder
{
    public function __call($name, $arguments)
    {
        $command = null;
        switch($name) {
            case Keywords::INSERT: 
                $command = 'insertQuery';
            break; 
            case Keywords::MULTISERT: 
                $command = 'insertQuery';
            break; 
        }
        
        call_user_func(array($this, $command), ...$arguments);
    }
}