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


    public static function parse(array $data, array $field_key_column)
    {
        $parse_data = [];
        foreach($field_key_column as $key => $value)
        {
            if(array_key_exists($key, $data)) {
                $_data = $data[$key];

                if ($value == 'array') {
                    $_data = json_decode($_data, TRUE);
                } else {
                    settype($_data, $value);
                }
                $parse_data[$key] = $_data;
            }
        }

        return $parse_data;
    }
}