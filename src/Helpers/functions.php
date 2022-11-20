<?php

if(!function_exists('array_map_key')) {
    function array_map_key(array $array, array $keys) 
    {
        $new_value = [];
        foreach($keys as $key => $value) {
            if (array_key_exists($key, $array)) {
                $array_val = $array[$key];
                $new_value[$key] = (is_array($array_val)) ? json_encode($array_val) : $array_val;
            }
        }
        return $new_value;
    }
}

if(!function_exists('is_multi_array')) {
    function is_multi_array(array $array) {
        rsort( $array );
        return isset( $array[0] ) && is_array( $array[0] );
    }
}

