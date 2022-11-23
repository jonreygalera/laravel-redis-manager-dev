<?php

namespace Jonreyg\LaravelRedisManager\Bridge\Traits;

use Exception;

trait Query 
{  
    use Operators;

    public function insertQuery(array $data)
    {
        $data = is_multi_array($data) ? $data : [$data];
        return $this->hmsetCommand($data);
    }


    public function findOrCreateQuery($hash_key_value, array $data)
    {
        return $this->findCommand($hash_key_value, function() use($data) {
            return $data;
        });
    }

    public function whereQuery(array $search, callable $fallback = null)
    {
        $this_data = $this->result;

        if (empty($this_data)) {
            $this_data = $this->all();
        }

        $this->result = array_filter($this_data, function($data) use($search) {
            foreach ($search as $key => $value) {
                if (array_key_exists($key, $data)) {
                    $column = $data[$key];

                    if (is_array($value)) {
                        if (empty($value)) throw new Exception('Error: unknown Redis Manager operator');
                        if (!(array_key_exists(0, $value)) || !(array_key_exists(1, $value))) throw new Exception('Error: missing format');
                        $is_good = $this->whereData($value[0], $column, $value[1]);
                        if($is_good) continue;
                        return false;

                    } else {
                        if($column == $value) {
                            continue;
                        } 
                        return false;
                    }
                   
                } else return false;
            }

            return true;
        });

        if (empty($this->result) && !is_null($fallback)) {
            $this->fallback($fallback)
                ->whereQuery($search);
        }

        return $this;
    }

    // public function orWhere(array $search, $fallback = null)
    // {
    //     $this_data = $this->result;

    //     if (empty($this_data)) {
    //         $this_data = $this->allByFolder();
    //     }

    //     $this->result = array_filter($this_data, function($data) use($search) {
    //         foreach ($search as $key => $value) {
    //             if (array_key_exists($key, $data)) {
    //                 $column = (gettype($data) === 'array') ? $data[$key] : $data->{$key};
    //                 if (is_array($value)) {
    //                     if (empty($value)) throw new \Exception('Error: unknown Redis Manager operator');
    //                     if (!(array_key_exists(0, $value)) || !(array_key_exists(1, $value))) throw new \Exception('Error: missing format');
    //                     $is_good = $this->whereData($value[0], $column, $value[1]);
    //                     if($is_good) return true;

    //                 } else {
    //                     if($column == $value) {
    //                         return true;
    //                     } 
    //                 }
                   
    //             } else throw new \Exception('Redis: Unknown field key');
    //         }

    //         return false;
    //     });
        
    //     if ($fallback) {
    //         return $this->fallback($fallback);
    //     }

    //     return $this;
    // }

    // public function whereIn($field_key, array $find, $fallback = null)
    // {
    //     $this_data = $this->result;

    //     if (empty($this_data)) {
    //         $this_data = $this->allByFolder();
    //     }

    //     $this->result = array_filter($this_data, function($data) use($field_key, $find) {
    //         if (array_key_exists($field_key, $data)) {

    //             if (in_array($data[$field_key], $find)) return true;

    //         } else throw new \Exception('Redis: Unknown field key');

    //         return false;
    //     });

    //     if ($fallback) {
    //         return $this->fallback($fallback);
    //     }

    //     return $this;
    // }

    // public function whereBetween($field_key, array $values, $fallback = null)
    // {
    //     $this_data = $this->result;

    //     if (empty($this_data)) {
    //         $this_data = $this->allByFolder();
    //     }

    //     if (gettype($values[0]) != gettype($values[1])) throw new \Exception('value must have the same type.');

    //     $is_string = gettype($values[0]) === 'string';

    //     $start_value = $is_string ? strtotime($values[0]) : $values[0];
    //     $end_value = $is_string ? strtotime($values[1]) : $values[1];

    //     $this->result = array_filter($this_data, function($data) use($field_key, $start_value, $end_value, $is_string) {
    //         if (array_key_exists($field_key, $data)) {
    //             if($is_string) {
    //                 $compare = strtotime($data[$field_key]);
    //                 if ($start_value <= $compare && $compare <= $end_value) return true;
    //             } else {
    //                 $compare = $data[$field_key];
    //                 if ($start_value >= $compare && $compare <= $end_value) return true;
    //             }
    //         } else throw new \Exception('Redis: Unknown field key');

    //         return false;
    //     });

    //     if ($fallback) {
    //         return $this->fallback($fallback);
    //     }

    //     return $this;
    // }

}