<?php

namespace App\Repositories\OooReason;

use Illuminate\Database\Eloquent\Model;

class OooReason extends Model
{
    const DEFAULT = 1;
    const NOT_DEFAULT = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'default'];
}