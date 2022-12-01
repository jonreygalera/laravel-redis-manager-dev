<?php

namespace Jonreyg\LaravelRedisManager\Bridge\Traits;

use Exception;
use Jonreyg\LaravelRedisManager\Redis;
use Jonreyg\LaravelRedisManager\Bridge\RedisManager;
use Jonreyg\LaravelRedisManager\Bridge\DataType;

trait Relations 
{
    public function joinCommand($associate_folder_class, $foreign_key)
    {

        if(Redis::canProceedOnDown()) {
            $this->result = [];
            return $this;
        }
        
        $associate_folder_instance = new $associate_folder_class;
        if(!($associate_folder_instance instanceof RedisManager)) throw new Exception('Invalid arguments.');
        $associate_folder = $associate_folder_instance->folder;
        $associate_field_key_column = $associate_folder_instance->field_key_column;

        $associate_folder_keys = Redis::keys("{$associate_folder}:*");

        $parent_data = $this->hgetallCommand();
     
        $new_data = [];
        foreach($parent_data as $key => $value) {
            $original_data = $value;
            $foreign_key_selected = $original_data[$foreign_key] ?? null;
            $original_data[$associate_folder] = null;
            if ($foreign_key_selected) {
                $original_data[$associate_folder] = DataType::parse(Redis::hgetall("{$associate_folder}:{$foreign_key_selected}"), $associate_field_key_column);
            }
            $new_data[] = $original_data;
        }

        $this->result = $new_data;

        return $this;
    }

}