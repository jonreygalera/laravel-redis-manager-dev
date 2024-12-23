<?php

namespace Jonreyg\LaravelRedisManager\Bridge;

use Jonreyg\LaravelRedisManager\Bridge\Model\DBLanguage;
use Jonreyg\LaravelRedisManager\Bridge\DataType;
use Jonreyg\LaravelRedisManager\Exceptions\PropertyException;
use Jonreyg\LaravelRedisManager\Redis;
use Illuminate\Support\Str;

abstract class RedisManager extends DBLanguage
{
    use Traits\Commands,
        Traits\Utility,
        Traits\Expiration;

    const HASH_KEY_ALL = '*';

    protected $folder;
    protected $hash_key = 'id'; // the value was taken to and must be present in $field_key_column
    protected $field_key_column = [];

    public $result = [];
    protected $folder_description = '';
    // protected $skip_empty = true;
    // public $redis_on = true;
    
    public function __construct()
    {
        config(['database.redis.options.prefix' => '']);
        Redis::checkRedisConnection();
        $this->checkFolderProperty()
            ->checkFieldKeyColumnProperty()
            ->checkHasKeyProperty();
    
        (new RedisFolder)->store([ 
            'folder_name' => $this->folder, 
            'manager' => get_called_class(),
            'folder_description' => $this->folder_description 
        ]);
    }

    public function getColumnList()
    {
        return $this->field_key_column;
    }

    public function getHashKey()
    {
        return $this->hash_key;
    }

    public function getFolderName()
    {
        return $this->folder;
    }

    public function getFolderDescription()
    {
        return $this->folder_description;
    }

    public function canProceedWhenDown()
    {
        return Redis::canProceedWhenDown();
    }

    public function checkFolderProperty()
    {
        if(!isset($this->folder)) throw new PropertyException('Redis `folder` is required.');
        if(!is_string($this->folder)) throw new PropertyException('Redis `folder` must be a string type value.');
        return $this;
    }

    public function checkHasKeyProperty()
    {
        if(!isset($this->hash_key)) throw new PropertyException('Redis `hash_key` is required.');
        if(!is_string($this->hash_key)) throw new PropertyException('Redis `hash_key` must be a string type value.');
        return $this;
    }

    public function checkFieldKeyColumnProperty()
    {
        if(!isset($this->field_key_column)) throw new PropertyException('Redis `field_key_column` is required.');
        if(!is_array($this->field_key_column)) throw new PropertyException('Redis `field_key_column` must be an array type value.');
        if(count($this->field_key_column) === 0) throw new PropertyException('Redis `field_key_column` must have an array value.');
        DataType::checkColumn($this->field_key_column);

        return $this;
    }

    public static function __callStatic($name, $arguments)
    {
        $method ="{$name}Command";
        return (new static)->$method(...$arguments);
    }

    public function __call($name, $arguments)
    {
        $method ="{$name}Command";
        return $this->$method(...$arguments);
    }
}
