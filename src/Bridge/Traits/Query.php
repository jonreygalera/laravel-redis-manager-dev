<?php

namespace Jonreyg\LaravelRedisManager\Bridge\Traits;

trait Query 
{  
    public function insertQuery(array $data)
    {
        return $this->multisertQuery([$data]);
    }

    public function multisertQuery(array $data)
    {
        $this->hmsetCommand($data);
        return $this;
    }


}