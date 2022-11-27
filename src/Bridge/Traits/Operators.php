<?php

namespace Jonreyg\LaravelRedisManager\Bridge\Traits;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Exception;

trait Operators 
{  
    use SpecialFunction;

    public $operators = [
        '=', '==', '===', '<', '>', '<=', '>=', '<>', '!=', '<=>',
        'contains', 'regexp'
    ];

    public function whereData($operator, $operand, $_operand)
    {
        if (!is_array($operator)) {
            if (!in_array($operator, $this->operators)) throw new Exception('Error: unknown redis manager operator');
            return $this->defaultOperator($operator, $operand, $_operand);
        }
        return $this->specialOperator($operator, $operand, $_operand);
    }

    public function equalOperator($operand, $_operand)
    {
        return $operand == $_operand;
    }

    public function strictEqualOperator($operand, $_operand)
    {
        return $operand === $_operand;
    }

    public function lessOperator($operand, $_operand)
    {
        return $operand < $_operand;
    }

    public function lessEqualOperator($operand, $_operand)
    {
        return $operand <= $_operand;
    }

    public function greaterOperator($operand, $_operand)
    {
        return $operand > $_operand;
    }

    public function greaterEqualOperator($operand, $_operand)
    {
        return $operand >= $_operand;
    }
    
    public function notEqualOperator($operand, $_operand)
    {
        return $operand != $_operand;
    }
    
    public function spaceshipOperator($operand, $_operand)
    {
        $output = $operand <=> $_operand;

        if ($output == 0) return $this->equalOperator($operand, $_operand);
        if ($output == 1) return $this->lessOperator($operand, $_operand);
        if ($output == -1) return $this->greaterOperator($operand, $_operand);

        return false;
    }

    public function containsOperator($operand, $_operand)
    {
        return Str::contains($operand, $_operand);
    }
    
    public function regexpOperator($operand, $_operand)
    {
        return preg_match("/$_operand/i", $operand);
    }

    public function defaultOperator($operator, $operand, $_operand)
    {
        if (in_array($operator, ['=', '=='])) return $this->equalOperator($operand, $_operand);
        if (in_array($operator, ['==='])) return $this->strictEqualOperator($operand, $_operand);
        if (in_array($operator, ['<>', '!='])) return $this->notEqualOperator($operand, $_operand);
        if ($operator == '<') return $this->lessOperator($operand, $_operand);
        if ($operator == '<=') return $this->lessEqualOperator($operand, $_operand);
        if ($operator == '>') return $this->greaterOperator($operand, $_operand);
        if ($operator == '>=') return $this->greaterEqualOperator($operand, $_operand);
        if ($operator == '<=>') return $this->spaceshipOperator($operand, $_operand);
        if ($operator == 'contains') return $this->containsOperator($operand, $_operand);
        if ($operator == 'regexp') return $this->regexpOperator($operand, $_operand);

        throw new Exception('Error: unknown redis manager operator');
    }
}