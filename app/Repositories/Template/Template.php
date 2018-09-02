<?php

namespace App\Repositories\Template;

use App\Repositories\TemplateOverdueReason\TemplateOverdueReason;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\TemplateNotification\TemplateNotification;
use App\Repositories\Group\Group;
use App\Repositories\User\User;
use App\Repositories\TemplateSubtask\TemplateSubtask;
use App\Repositories\TemplateVersion\TemplateVersion;
use App\Repositories\Task\Task;

class Template extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category',
        'title',
        'active',
    ];

    /**
     * @var array
     */
    public static $notificationDynamicVariables = ['clientname', 'taskname', 'deadline'];

    /**
     * The users that belong to the template.
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * The subtasks owned by the template.
     */
    public function subtasks()
    {
        return $this->hasMany(TemplateSubtask::class);
    }

    /**
     * The tasks owned by the template.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications()
    {
        return $this->hasMany(TemplateNotification::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function overdueReasons()
    {
        return $this->hasMany(TemplateOverdueReason::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function versions()
    {
        return $this->hasMany(TemplateVersion::class,'template_id','id')->orderBy('id', 'DESC');
    }
}
