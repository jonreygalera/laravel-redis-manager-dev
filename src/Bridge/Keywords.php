<?php

namespace Jonreyg\LaravelRedisManager\Bridge;

abstract class Keywords
{
    // Query
    const INSERT = 'insert';
    const MULTISERT = 'multisert';

    // Redis
    const KEYS = 'keys';

    // Expiration
    const ADDEXPIRATION = 'addExpiration';
    const EXPIREHOURS = 'expireHours';
    const EXPIREHOUR = 'expireHour';
    const EXPIREHALFHOUR = 'expireHalfHour';
    const EXPIREDAYS = 'expireDays';
    const EXPIREDAY = 'expireDay';
    const EXPIREWEEK = 'expireWeek';
    const EXPIREWEEKS = 'expireWeeks';
    const EXPIREMINUTES = 'expireMinutes';
    const EXPIREMINUTE = 'expireMinute';
    const EXPIREYEARS = 'expireYears';
    const EXPIREYEAR = 'expireYear';
}