<?php

namespace App\Repositories\CustomerType;

use Illuminate\Database\Eloquent\Model;

class CustomerType extends Model
{
    protected $table = 'customer_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

}
