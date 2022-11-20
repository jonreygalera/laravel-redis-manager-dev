<?php

namespace Jonreyg\LaravelRedisManager\Bridge\Traits;

use Jonreyg\LaravelRedisManager\Redis;
use Jonreyg\LaravelRedisManager\Bridge\Keywords;

trait NonStaticMethodBuilder
{
    public function __call($name, $arguments)
    {
        $command = null;
        switch($name) {
            // Query
            case Keywords::INSERT: 
                $command = 'insertQuery';
            break; 
            case Keywords::ALL: 
                $command = 'allQuery';
            break; 

            // Expiration
            case Keywords::ADDEXPIRATION:
                $command = 'addExpirationCommand';
            break;
            case Keywords::EXPIREHOURS:
                $command = 'expireHoursCommand';
            break;
            case Keywords::EXPIREHOUR:
                $command = 'expireHourCommand';
            break;
            case Keywords::EXPIREHALFHOUR:
                $command = 'expireHalfHourCommand';
            break;
            case Keywords::EXPIREDAYS:
                $command = 'expireDaysCommand';
            break;
            case Keywords::EXPIREDAY:
                $command = 'expireDayCommand';
            break;
            case Keywords::EXPIREWEEK:
                $command = 'expireWeekCommand';
            break;
            case Keywords::EXPIREWEEKS:
                $command = 'expireWeeksCommand';
            break;
            case Keywords::EXPIREMINUTES:
                $command = 'expireMinutesCommand';
            break;
            case Keywords::EXPIREMINUTE:
                $command = 'expireMinuteCommand';
            break;
            case Keywords::EXPIREYEARS:
                $command = 'expireYearsCommand';
            break;
            case Keywords::EXPIREYEAR:
                $command = 'expireYearCommand';
            break;            
        }

        return call_user_func(array($this, $command), ...$arguments);
    }
}