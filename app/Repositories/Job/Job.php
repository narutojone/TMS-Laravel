<?php

namespace App\Repositories\Job;

use Illuminate\Database\Eloquent\Model;

/** 
 * THe model for the Job entity
*/
class Job extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'queue',
        'payload',
        'attempts',
        'reserved_at',
        'available_at',
        'created_at'
    ];

    /**
     * Get the user that owns the note.
     */
    public function user()
    {
        // Here if we sould have a repository for User the path of the model will be diffrent
        return $this->belongsTo('App\Repositories\User\User');
    }
}