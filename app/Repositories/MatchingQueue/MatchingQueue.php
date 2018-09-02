<?php

namespace App\Repositories\MatchingQueue;

use Illuminate\Database\Eloquent\Model;

/** 
 * THe model for the MatchingQueue entity
*/
class MatchingQueue extends Model
{
    protected $table = 'matching_queue';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'matched',
        'client_id'
    ];
}