<?php

if(!function_exists('array_map_key')) {
    function array_map_key(array $array, array $keys) 
    {
        $new_value = [];
        foreach($keys as $key => $value) {
            if (array_key_exists($key, $array)) {
                $array_val = (gettype($array) === 'array') ? $array[$key] : $array->{$key};
                $new_value[$key] = (is_array($array_val)) ? json_encode($array_val) : $array_val;
            }
        }
        return $new_value;
    }
}