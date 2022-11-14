<?php

namespace Jonreyg\LaravelRedisManager\Bridge;

use Jonreyg\LaravelRedisManager\Bridge\Model\Relations;

abstract class RedisManager extends Relations
{
    use Traits\Commands,
        Traits\StaticMethodBuilder,
        Traits\NonStaticMethodBuilder;

    const HASH_KEY_ALL = '*';

    protected $folder;
    protected $hash_key = 'id'; // the value was taken to and must be present in $field_key_column
    protected $field_key_column = [];
    protected $save_folder_name = true;
    protected $skip_empty = true;
    public $redis_on = true;

    public function __construct()
    {
        // $namespace = explode('\\', get_called_class());
        // dd(get_called_class());
    }
}