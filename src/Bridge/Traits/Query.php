<?php

namespace Jonreyg\LaravelRedisManager\Bridge\Traits;

trait Query 
{  
    public function insertQuery(array $data)
    {
        $data = is_multi_array($data) ? $data : [$data];
        return $this->hmsetCommand($data);
    }
   
    public function allQuery($callable = null)
    {
        return $this->hgetallCommand($callable);
    }
}