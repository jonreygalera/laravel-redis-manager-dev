<?php

namespace Jonreyg\LaravelRedisManager\Bridge\Traits;

trait Expiration {
    public $expire_at = 3600;
    public $with_expiration = TRUE;

    public function addExpiration(float $seconds)
    {
        $this->with_expiration = TRUE;
        $this->expire_at = (int) $seconds;
        return $this;
    }

    public function expireHours(float $hours)
    {
        $seconds = $hours * 60 * 60;
        return $this->addExpiration($seconds);
    }

    public function expireHour()
    {
        return $this->expireHours(1);
    }

    public function expireHalfHour()
    {
        return $this->expireHours(0.5);
    }

    public function expireDays(float $days)
    {
        $hours = $days * 24;
        return $this->expireHours($hours);
    }

    public function expireDay()
    {
        return $this->expireDays(1);
    }

    public function expireWeek()
    {
        return $this->expireWeeks(1);
    }

    public function expireWeeks(float $weeks)
    {
        $week = 7 * $weeks;
        $seconds = (3600 * 24) * $week;
        return $this->addExpiration($seconds);
    }

    public function expireMinutes(float $minutes)
    {
        $seconds = $minutes * 60;
        return $this->addExpiration($seconds);
    }

    public function expireMinute()
    {
        return $this->expireMinutes(1);
    }

    public function expireYears(float $years)
    {
        $seconds = 31556952 * $years;
        return $this->addExpiration($seconds);
    }

    public function expireYear()
    {
        $seconds = 31556952;
        return $this->expireYears($seconds);
    }
}