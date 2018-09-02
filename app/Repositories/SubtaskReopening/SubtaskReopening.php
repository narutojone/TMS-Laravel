<?php

namespace App\Repositories\SubtaskReopening;

use Illuminate\Database\Eloquent\Model;

class SubtaskReopening extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'reason',
        'completed_at',
        'subtask_id',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'completed_at',
        'created_at',
        'updated_at',
    ];
}
