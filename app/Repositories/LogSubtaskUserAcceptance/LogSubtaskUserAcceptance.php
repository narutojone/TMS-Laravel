<?php

namespace App\Repositories\LogSubtaskUserAcceptance;

use Illuminate\Database\Eloquent\Model;

/** 
 * THe model for the LogSubtaskUserAcceptance entity
*/
class LogSubtaskUserAcceptance extends Model
{
    protected $table = 'logs_subtask_user_acceptance';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'subtask_template_id',
        'version_no',
        'terms_accepted'
    ];

}