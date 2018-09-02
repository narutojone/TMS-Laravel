<?php

namespace App\Filters;

class InformationFilters extends QueryFilters
{
    /**
     * @param $value
     */
    public function search($value)
    {
        $this->builder->where('title', 'like', "%{$value}%");
    }

    /**
     * @param $value
     */
    public function status($value)
    {
        $this->builder->where('accepted_status', $value);
    }

    /**
     * @param $value
     */
    public function date($value)
    {
        $this->builder->where('information_user.created_at', 'like', "%{$value}%");
    }
}
