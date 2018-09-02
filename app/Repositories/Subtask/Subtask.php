<?php

namespace App\Repositories\Subtask;

use App\Repositories\File\File;
use App\Repositories\SubtaskReopening\SubtaskReopening;
use App\Repositories\Task\Task;

use App\Repositories\TasksUserAcceptance\TasksUserAcceptance;
use App\Repositories\TemplateSubtask\TemplateSubtask;
use App\Repositories\TemplateSubtaskVersion\TemplateSubtaskVersion;
use App\Repositories\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Subtask extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order',
        'title',
        'task_id',
        'description',
        'subtaskTemplateId',
        'version_no',
        'user_id',
        'completed_at',
        'upload_not_needed_reason',
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
    
    /**
     * Scope a query to only include uncompleted subtasks.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUncompleted($query)
    {
        return $query->whereNull('completed_at');
    }

    /**
     * Get the reopenings for the subtask.
     */
    public function reopenings()
    {
        return $this->hasMany(SubtaskReopening::class);
    }

    /**
     * Check if subtask is reopened
     *
     * @return bool
     */
    public function isReopened()
    {
        return (bool) $this->reopenings()->count();
    }

    /**
     * Get the task that owns the subtask.
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the version on which the subtask was created (it may NOT be the latest version no.)
     */
    public function version()
    {
        return $this->hasOne(TemplateSubtaskVersion::class, 'subtask_template_id', 'subtaskTemplateId')->where('version_no', $this->version_no);
    }

    /**
     * Return whether the subtask is completed or not.
     */
    public function isComplete()
    {
        return !is_null($this->completed_at);
    }

    /**
     * Get the user that owns the subtask.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the template that owns the subtask.
     */
    public function template()
    {
        return $this->belongsTo(TemplateSubtask::class, 'subtaskTemplateId', 'id');
    }

    public function changesAccepted()
    {
        // some subtasks may not have template (old templates that kept changind before template id was available to subtasks)
        if(!$this->template) return true;

        return TasksUserAcceptance::where('type', 2)->where('user_id', Auth::user()->id)->where('template_id', $this->template->id)->where('version_no', $this->version_no)->exists();
    }

    public function needsReview()
    {
        if($this->changesAccepted()) return false;

        return true;
    }

    public function files()
    {
        return $this->hasMany(File::class, 'subtask_id', 'id');
    }
}
