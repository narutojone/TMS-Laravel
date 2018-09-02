<?php

namespace App\Repositories\LogTaskUserAcceptance;

use App\Repositories\User\User;
use Illuminate\Database\Eloquent\Model;

/** 
 * THe model for the LogTaskUserAcceptance entity
*/
class LogTaskUserAcceptance extends Model
{
    protected $table = 'logs_task_user_acceptance';

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'template_id',
        'version_no',
        'terms_accepted'
    ];

    /**
     * Get the user that owns the note.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}