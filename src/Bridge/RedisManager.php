<?php

namespace Jonreyg\LaravelRedisManager\Bridge;

use Jonreyg\LaravelRedisManager\Bridge\Model\Relations;
use Jonreyg\LaravelRedisManager\Bridge\DataType;
use Jonreyg\LaravelRedisManager\Exceptions\PropertyException;

abstract class RedisManager extends Relations
{
    use Traits\Commands,
        Traits\Expiration,
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
        $this->checkFolderProperty()
            ->checkHasKeyProperty()
            ->checkFieldKeyColumnProperty();
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
}