<?php

namespace App\Repositories\FailedJob;

use Illuminate\Database\Eloquent\Model;

/** 
 * THe model for the FailedJob entity
*/
class FailedJob extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'connection',
        'queue',
        'payload',
        'exception'
    ];
}