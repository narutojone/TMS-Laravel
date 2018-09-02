<?php

namespace App\Filters\Traits;

use App\Filters\QueryFilters;
use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    /**
     * Filter results
     *
     * @param Builder $builder
     * @param QueryFilters $filters
     * @return Builder
     */
    public function scopeFilter(Builder $builder, QueryFilters $filters)
    {
        return $filters->apply($builder);
    }
}
