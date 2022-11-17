<?php

namespace Jonreyg\LaravelRedisManager\Bridge\Traits;

trait Expiration {
    public $expire_at = 3600;
    public $with_expiration = TRUE;

    public function addExpirationCommand(float $seconds)
    {
        $this->with_expiration = TRUE;
        $this->expire_at = (int) $seconds;
        return $this;
    }

    public function expireHoursCommand(float $hours)
    {
        $seconds = $hours * 60 * 60;
        return $this->addExpirationCommand($seconds);
    }

    public function expireHourCommand()
    {
        return $this->expireHoursCommand(1);
    }

    public function expireHalfHourCommand()
    {
        return $this->expireHours(0.5);
    }

    public function expireDaysCommand(float $days)
    {
        $hours = $days * 24;
        return $this->expireHours($hours);
    }

    public function expireDayCommand()
    {
        return $this->expireDaysCommand(1);
    }

    public function expireWeekCommand()
    {
        return $this->expireWeeksCommand(1);
    }

    public function expireWeeksCommand(float $weeks)
    {
        $week = 7 * $weeks;
        $seconds = (3600 * 24) * $week;
        return $this->addExpirationCommand($seconds);
    }

    public function expireMinutesCommand(float $minutes)
    {
        $seconds = $minutes * 60;
        return $this->addExpirationCommand($seconds);
    }

    public function expireMinuteCommand()
    {
        return $this->expireMinutesCommand(1);
    }

    public function expireYearsCommand(float $years)
    {
        $seconds = 31556952 * $years;
        return $this->addExpirationCommand($seconds);
    }

    public function expireYearCommand()
    {
        $seconds = 31556952;
        return $this->expireYearsCommand($seconds);
    }
}