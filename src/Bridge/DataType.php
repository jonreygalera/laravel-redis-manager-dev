<?php

namespace Jonreyg\LaravelRedisManager\Bridge;
use Jonreyg\LaravelRedisManager\Exceptions\DataTypeException;

class DataType
{
    const DINT = 'int';
    const DFLOAT = 'float';
    const DSTRING = 'string';
    const DARRAY = 'array';
    const DBOOL = 'bool';

    public static function isDataType(string $data_type)
    {
        return in_array($data_type, [
            self::DINT,
            self::DFLOAT,
            self::DSTRING,
            self::DARRAY,
            self::DBOOL
        ]);
    }

    public static function checkColumn(array $field_key_column)
    {
        foreach($field_key_column as $key => $value) {
            if(!self::isDataType($value)) throw new DataTypeException("Unknown `{$value}` data type.");
        }

        return true;
    }
}