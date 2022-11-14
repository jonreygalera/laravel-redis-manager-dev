<?php

namespace Jonreyg\LaravelRedisManager\Bridge\Traits;

use Illuminate\Support\Facades\Redis;

trait Commands
{    
    public function hmsetCommand(array $data)
    {
        if (!is_array($this->field_key_column)) throw new \Exception('Field key column must be an array.');
        $folder = $this->folder;

        foreach($data as $key => $value) {
            $field_key = gettype($value) == 'array' ? $value[$this->hash_key] : $value->{$this->hash_key};
            dd($field_key, $value);
            $save = $this->array_key_map($value);
            $set_key = $folder.":{$field_key}";

            // Redis::hmset($set_key, $save);

            // if ($this->with_expiration) {
            //     Redis::expire($set_key, $this->expire_at);
            // }
            dd($set_key, $save);
        }

        return $this;
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