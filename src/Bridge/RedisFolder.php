<?php

namespace Jonreyg\LaravelRedisManager\Bridge;

use Jonreyg\LaravelRedisManager\Redis;

class RedisFolder
{
    protected $folder = 'redis_folder';
    protected $hash_key = 'folder_name';

    public static function store($data)
    {
        if (!Redis::canProceedOnDown()) {
            $self = new self;

            Redis::hmset("{$self->folder}:" . $data[$self->hash_key], $data);
        }
    }

    public static function flushFolder($folder_name, $from_redis_folder = false)
    {
        if (!Redis::canProceedOnDown()) {
            if ($folder_name === (new self)->folder) return;

            $keys = Redis::keys("{$folder_name}:*");
            $self = new self;
            if (empty($keys) && (!$from_redis_folder)) {

                Redis::del("{$self->folder}:{$folder_name}");
                throw new \Exception("Unknown `{$folder_name}` redis folder");
            }
            if (empty($keys) && $from_redis_folder) return;
            Redis::del($keys);
            Redis::del("{$self->folder}:{$folder_name}");
        }
    }

    public static function flushDb()
    {
        if (!Redis::canProceedOnDown()) {
            $self = new self;
            $keys = Redis::keys("{$self->folder}:*");

            foreach ($keys as $key) {
                $folder_name = explode(':', $key);
                self::flushFolder($folder_name[1], true);
            }

            $keys = Redis::keys("{$self->folder}:*");
            Redis::del($keys);
        }

        return true;
    }

    public static function all()
    {
        $data = [];
        if (!Redis::canProceedOnDown()) {
            $self = new self;
            $keys = Redis::keys("{$self->folder}:*");

            foreach($keys as $key) {
                $data[] = Redis::hmget($key, ['folder_name'])[0] ?? '';
            }
        }
        
        return $data;
    }

    public static function getFolder($folder)
    {
        $data = [];
        if (!Redis::canProceedOnDown()) {
            $self = new self;
            $data = Redis::hmget("{$self->folder}:{$folder}", ['folder_name', 'manager', 'folder_description']);
        }
        
        return $data;
    }
}