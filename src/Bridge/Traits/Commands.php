<?php

namespace Jonreyg\LaravelRedisManager\Bridge\Traits;

use Jonreyg\LaravelRedisManager\Exceptions\HashKeyException;
use Jonreyg\LaravelRedisManager\Redis;
use Jonreyg\LaravelRedisManager\Bridge\DataType;
use Exception;

trait Commands
{    
    public function hmsetCommand(array $data)
    {
       return $this->ductCommand(function() use($data) {
            if (!is_array($this->field_key_column)) throw new \Exception("Field key column must be an array.");
            $folder = $this->folder;
            $columns = $this->field_key_column;
        
            Redis::pipeline(function($pipe) use($data, $folder, $columns) {
                foreach($data as $key => $value) {
                    if(!array_key_exists($this->hash_key, $value)) throw new HashKeyException("Cannot find hash_key `{$this->hash_key}.`");
                    $field_key = $value[$this->hash_key];
        
                    $save = array_map_key($value, $columns);
                    $set_key = $folder.":{$field_key}";
        
                    $pipe->hmset($set_key, $save);
        
                    if ($this->with_expiration) {
                        $pipe->expire($set_key, $this->expire_at);
                    }
                }
        
            });
            $this->result = $data;
            return $this;
       }, function() use($data) {
            return $data;
       });
    }

    public function keysCommand($hash_key_value = null)
    {
        $hash_key_value = $hash_key_value ? $hash_key_value : self::HASH_KEY_ALL;
        return Redis::keys($this->folder.":" . $hash_key_value);
    }

    public function hgetallCommand(callable $fallback = null)
    {
        return $this->ductCommand(function() use($fallback) {
            $new_data = [];
            $keys = $this->keysCommand();
            foreach($keys as $key) {
                $new_data[] = DataType::parse(Redis::hgetall($key), $this->field_key_column);
            }

            return $this->dataCheckerCommand($new_data, $fallback);
        }, $fallback)->get();
    }

    public function findCommand($hash_key_value, callable $fallback = null)
    {
        return $this->ductCommand(function() use($hash_key_value, $fallback) {
            $new_data = [];
            $keys = $this->keysCommand($hash_key_value);
            foreach($keys as $key) {
                $new_data[] = DataType::parse(Redis::hgetall($key), $this->field_key_column);
            }
            
            if(empty($new_data)) {
                $fallback_data = $fallback();
                if(!is_array($fallback_data)) throw new Exception("Fallback data must be an array.");
                if(!array_key_exists($this->hash_key, $fallback_data)) throw new Exception("Hash key `{$this->hash_key}` not found.");
                if (!($fallback_data[$this->hash_key] === $hash_key_value)) throw new Exception("hash key value `{$hash_key_value}` did not match to the fallback data `{$this->hash_key}` value.");;
            }

            return $this->dataCheckerCommand($new_data, $fallback);
        }, $fallback)->first();
    }

    public function existsCommand($hash_key_value)
    {
        $folder = "{$this->folder}:{$hash_key_value}";
        return boolval(Redis::exists($folder));
    }

    public function deleteCommand($hash_key_value)
    {
        return Redis::del("{$this->folder}:{$hash_key_value}");
    }
}