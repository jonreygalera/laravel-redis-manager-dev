<?php

namespace Jonreyg\LaravelRedisManager\Bridge\Traits;

use Jonreyg\LaravelRedisManager\Redis;

trait Utility
{
    public function fallbackCommand(callable $callable)
    {
        return $this->insertQuery($callable($this));
    }

    public function dataCheckerCommand(array $data, callable $callable = null)
    {
        if(!$callable) {
            $this->result = $data;
            return $this;
        }

        if (!empty($data)) {
            $this->result = $data;
        } else {
            $this->fallbackCommand($callable);
        }

        return $this;
    }

    public function ductCommand(callable $callable, callable $fallback = null)
    {
        if (Redis::canProceedOnDown()) {
            $this->result = ($fallback) ? $fallback($this) : [];
        } else {
            $callable($this);
        }

        return $this;
    }

    public function first($fields = [])
    {
        return current($this->get($fields)) ?? [];
    }

    public function get($fields = [])
    {
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