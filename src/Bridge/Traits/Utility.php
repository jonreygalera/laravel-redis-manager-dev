<?php

namespace Jonreyg\LaravelRedisManager\Bridge\Traits;

use Jonreyg\LaravelRedisManager\Redis;

trait Utility
{
    public function always(callable $callable)
    {
        $callable($this);
        return $this;
    }
    
    public function fallback(callable $callable)
    {
        $data = $callable($this);
        return $this->insertCommand($data);
    }

    public function dataCheckerCommand(array $data, callable $callable = null)
    {
        if(!$callable) {
            $this->result = $data;
            return $this;
        }

        if (!empty($data)) {
            $this->result = $data;
            return $this;
        } else {
            return $this->fallback($callable);
        }
    }

    public function ductCommand(callable $callable, callable $fallback = null)
    {
        if (Redis::canProceedOnDown()) {
            $this->result = ($fallback) ? $fallback($this) : [];
        } else {
            return $callable($this);
        }
    }

    public function firstCommand($fields = [])
    {
        return current($this->getCommand($fields)) ? current($this->getCommand($fields)) : null;
    }

    public function getCommand($fields = [])
    {
        $this->result = empty($this->result) ? $this->allCommand() : $this->result;

        $fields = is_array($fields) ? $fields : func_get_args();
        if (empty($fields)) return $this->result;

        $new_data = [];

        foreach($this->result as $data) {
            $selected_data = [];
            foreach ($fields as $field) {
                if (array_key_exists($field, $data)) {
                    $selected_data[$field] = $data[$field];
                } else throw new \Exception('Redis: Unknown field key');
            }

            $new_data[] = $selected_data;
        }

        return $new_data;
    }
}