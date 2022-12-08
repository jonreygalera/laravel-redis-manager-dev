<?php

namespace Jonreyg\LaravelRedisManager\Bridge\Traits;

use Jonreyg\LaravelRedisManager\Redis;
use Exception;

trait Utility
{
    public $offset_page_property;
    public $offset_property;
    public $limit_property;
    public $sortby_property = SORT_ASC;
    public $orderby_property;
    
    public $sortby_str_property = 'asc';
    
    public $sortby_asc = SORT_ASC;
    public $sortby_desc = SORT_DESC;

    public function orderByCommand($orderby_property, $sortby_property = 'ASC')
    {   
        $sortby_property = strtolower($sortby_property);

        if(!in_array($sortby_property, [ 'asc',  'desc'])) throw new Exception("`{$sortby_property}` sort by not match.");
        $sort_type = "sortby_{$sortby_property}";
        $this->orderby_property = $orderby_property;
        $this->sortby_property = $this->$sort_type;
        $this->sortby_str_property = $sortby_property;
        return $this;
    }

    public function limitCommand(int $limit_property)
    {
        $this->limit_property = $limit_property;
        return $this;
    }

    public function offsetCommand(int $offset_property)
    {
        $this->offset_property = $offset_property;
        $this->offset_page_property = $this->offsetPageGenerator($offset_property, $this->limit_property);
        return $this;
    }

    public function alwaysCommand(callable $callable)
    {
        $callable($this);
        return $this;
    }
    
    public function fallbackCommand(callable $callable)
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
            return $this->fallbackCommand($callable);
        }
    }

    public function ductCommand(callable $callable, callable $fallback = null)
    {
        if (Redis::canProceedOnDown()) {
            $this->result = ($fallback) ? $fallback($this) : [];
            return $this;
        } else {
            return $callable($this);
        }
    }

    public function firstCommand($fields = [])
    {
        if (Redis::canProceedOnDown()) return null;

        $this_data = $this->result ?? [];
        if (
            (!empty($this->offset_property)) ||
            (!empty($this->limit_property))
        ) {
            $this_data = $this->paginateCommand($this->offset_property ?? 0, $this->limit_property ?? 15, true);
        } else {
            if (!empty($this->orderby_property)) {
                $this_data = (empty($this_data)) ? $this->emptyOrAllCommand() : $this_data;
                $this_data = $this->orderByBuilder($this_data, $this->orderby_property, $this->sortby_property);
            }
        }

        $data = empty($this_data) ? current($this->emptyOrAllCommand()) : current($this_data ?? []);

        return ($data === false) ? null : $data;
    }


    public function paginateCommand(int $offset_property = 0, int $limit_property = 15, bool $data_only = false)
    {

        $result = [];

        $result["total"] = 0;
        $result["offset"] = (int) 0;
        $result["limit"] = (int) 15;
        $result["next_page"] = null;
        $result["previous_page"] = null;
        $result["has_next_page"] = true;
        $result["has_previous_page"] = true;
        $result["data"] = $this->result ?? [];

        if (Redis::canProceedOnDown()) return ($data_only) ? $result["data"] : $result;
        $this_data = (empty($this->result)) ? $this->emptyOrAllCommand() : $this->result;
    
        if (!empty($this->orderby_property)) {
            $this_data = $this->orderByBuilder($this_data, $this->orderby_property, $this->sortby_property);
            $result["orderby"] = $this->orderby_property;
            $result["sortby"] = $this->sortby_str_property ?? 'asc';
        }

        $offset_page_property = $this->offsetPageGenerator($offset_property, $limit_property);

        $total_data = count($this_data);
        $next_page = ($total_data - $offset_page_property <= $limit_property) ? null : $offset_property + 1;
        $previous_page = ($offset_property === 0) ? null : (int) $offset_property;

        $result["total"] = $total_data;
        $result["offset"] = (int) $offset_property;
        $result["limit"] = (int) $limit_property;
        $result["next_page"] = $next_page;
        $result["previous_page"] = $previous_page;
        $result["has_next_page"] = !is_null($next_page);
        $result["has_previous_page"] = !is_null($previous_page);
        $result["data"] = array_slice($this_data, $offset_page_property, $limit_property);

        return ($data_only) ? $result["data"] : $result;
    }

    public function countCommand()
    {
        if (Redis::canProceedOnDown()) return count($this->result ?? []);

        if (
            (!empty($this->offset_property)) ||
            (!empty($this->limit_property))
        ) return count($this->paginateCommand($this->offset_property ?? 0, $this->limit_property ?? 15, true));

        $this_data = $this->result ?? [];

        if (!empty($this->orderby_property)) {
            $this_data = (empty($this->result)) ? $this->emptyOrAllCommand() : $this->result;
            $this_data = $this->orderByBuilder($this_data, $this->orderby_property, $this->sortby_property);
        }

        return count($this_data);
    }


    public function getCommand()
    {
        if (Redis::canProceedOnDown()) return [];

        if (
            (!empty($this->offset_property)) ||
            (!empty($this->limit_property))
        ) return $this->paginateCommand($this->offset_property ?? 0, $this->limit_property ?? 15, true);

        $this_data = $this->result ?? [];

        if (!empty($this->orderby_property)) {
            $this_data = (empty($this->result)) ? $this->emptyOrAllCommand() : $this->result;
            $this_data = $this->orderByBuilder($this_data, $this->orderby_property, $this->sortby_property);
        }
        return $this_data;
    }

    private function orderByBuilder(array $data, string $orderby_property,  $sort_by_property = SORT_ASC)
    {
        array_multisort(array_column($data, $orderby_property), $sort_by_property, $data);
        
        return $data;
    }

    private function offsetPageGenerator($offset_property = 0, $limit_property = 15)
    {
        return $limit_property  * $offset_property;
    }
}