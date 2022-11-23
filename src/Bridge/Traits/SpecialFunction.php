<?php

namespace Jonreyg\LaravelRedisManager\Bridge\Traits;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Exception;

trait SpecialFunction 
{  
    public $action_method = [
        'date_format', 'parse_int', 'parse_double', 'parse_float', 'parse_boolean',
        'parse_string', 'length'
    ];

    public function specialOperator($operator, $operand, $_operand)
    {
        if (!is_array($operator)) throw new Exception('Error: Special Operator must be in array');
        
        if (array_key_exists('action', $operator)) {

            if (!array_key_exists('operator', $operator)) throw new Exception('Error: missing operator key.');
            $opt = $operator['operator'];

            $key = null;
            $params = null;

            if(is_array($operator['action'])) {
                $action = array_keys($operator['action']);
                if(empty($action)) throw new Exception('Error: action is empty');
                $key = $action[0];
                $params = $operator['action'][$key];
            } else {
                $key = $operator['action'];
            }

            if (Str::of($key)->trim()->isEmpty()) throw new Exception('Error: unknown action');
            if(!in_array($key, $this->action_method)) throw new Exception('Error: unknown action');

            $method = Str::camel($key);
            return $this->{$method}($opt, $operand, $_operand, $params);
        } else {
            if (array_key_exists('custom_action', $operator)) {
                if(!is_callable($operator['custom_action'])) throw new Exception('Error: custom action must be a function');
                
                $result = $operator['custom_action']($operand, $_operand);

                if (!is_bool($result)) throw new Exception('Error: custom action must return a boolean value');

                return $result;
            }
        }

        throw new Exception('Error: no action to execute');
    }


    public function dateFormat($operator, $operand_date, $_operand_date, $params)
    {
        $operand = Carbon::parse($operand_date)->format($params);
        $_operand = Carbon::parse($_operand_date)->format($params);

        if (!in_array($operator, $this->operators)) throw new Exception('Error: unknown redis manager operator');
        
        return $this->defaultOperator($operator, $operand, $_operand);
    }

    public function length($operator, $operand, $_operand, $params)
    {
        if (!in_array($operator, $this->operators)) throw new Exception('Error: unknown redis manager operator');
        
        $operand = count(str_split($operand));
        return $this->defaultOperator($operator, $operand, $_operand);
    }

    public function parseInt($operator, $operand, $_operand)
    {
        if (!in_array($operator, $this->operators)) throw new Exception('Error: unknown redis manager operator');
        return $this->parseDataType('int', $operator, $operand, $_operand);
    }
    
    public function parseDouble($operator, $operand, $_operand)
    {
        if (!in_array($operator, $this->operators)) throw new Exception('Error: unknown redis manager operator');
        return $this->parseDataType('double', $operator, $operand, $_operand);
    }
    
    public function parseFloat($operator, $operand, $_operand)
    {
        if (!in_array($operator, $this->operators)) throw new Exception('Error: unknown redis manager operator');
        return $this->parseDataType('float', $operator, $operand, $_operand);
    }
    
    public function parseBoolean($operator, $operand, $_operand)
    {
        if (!in_array($operator, $this->operators)) throw new Exception('Error: unknown redis manager operator');
        return $this->parseDataType('bool', $operator, $operand, $_operand);
    }
    
    public function parseString($operator, $operand, $_operand)
    {
        if (!in_array($operator, $this->operators)) throw new Exception('Error: unknown redis manager operator');
        return $this->parseDataType('string', $operator, $operand, $_operand);
    }

    public function parseDataType($type, $operator, $operand, $_operand)
    {
        settype($operand, $type);
        return $this->defaultOperator($operator, $operand, $_operand);
    }

}