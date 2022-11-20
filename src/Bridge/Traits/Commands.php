<?php

namespace Jonreyg\LaravelRedisManager\Bridge\Traits;

use Jonreyg\LaravelRedisManager\Exceptions\HashKeyException;
use Jonreyg\LaravelRedisManager\Redis;
use Jonreyg\LaravelRedisManager\Bridge\DataType;

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

    public function keysCommand()
    {
        return Redis::keys($this->folder.":".self::HASH_KEY_ALL);
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

    // public function _hmget($hash_key_value, array $fields = [])
    // {
    //     $folder = $this->folder;
    //     $folder .=  $hash_key_value ? ":{$hash_key_value}" : '';
    //     $fields = count($fields) === 0 ? array_keys($this->field_key_column) : $fields;
    //     return \Redis::hmget($folder, $fields);
    // }

    // public function _hgetall($hash_key_value = null)
    // {
    //     $new_data = [];
    //     $folder = $this->folder;
    //     $folder .=  $hash_key_value ? ":{$hash_key_value}" : '';

    //     if ($hash_key_value != self::HASH_KEY_ALL) return \Redis::hgetall($folder);

    //     $keys = $this->_keys();

    //     foreach($keys as $key) {
    //         $new_data[] = \Redis::hgetall($key);
    //     }
    //     return $new_data;
    // }


    // public function _exists($folder = null)
    // {
    //     $folder = $folder ?? $this->folder;

    //     return boolval(\Redis::exists($folder));
    // }

    // public function _hexists($field_key)
    // {
    //     if (!$this->_exists()) return false;

    //     $folder = $this->folder;

    //     return boolval(\Redis::hexists("{$folder}", $field_key));
    // }

    // public function _keys()
    // {
    //     return \Redis::keys($this->folder.":".self::HASH_KEY_ALL);
    // }

    // protected function _flushDB()
    // {
    //     return \Redis::flushDB();
    // }

    // public function _del($field_key)
    // {
    //     return \Redis::del("{$this->folder}:{$field_key}");
    // }
}