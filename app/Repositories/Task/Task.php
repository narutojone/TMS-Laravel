<?php

namespace App\Repositories\Task;

use App\Repositories\Client\Client;
use App\Repositories\Comment\Comment;
use App\Repositories\Review\Review;
use App\Repositories\ProcessedNotification\ProcessedNotification;
use App\Repositories\Subtask\Subtask;
use App\Repositories\TaskDetails\TaskDetails;
use App\Repositories\TaskOverdueReason\TaskOverdueReason;
use App\Repositories\TaskReopening\TaskReopening;
use App\Repositories\Template\Template;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Repositories\User\User;
use App\Repositories\TemplateNotification\TemplateNotification;
use App\Repositories\TemplateVersion\TemplateVersion;

class Task extends Model
{
    const ACTIVE = 1;
    const NOT_ACTIVE = 0;

    const REGENERATED = 1;
    const NOT_REGENERATED = 0;

    const PRIVATE = 1;
    const NOT_PRIVATE = 0;

    const DELIVERED = 1;
    const NOT_DELIVERED = 0;

    const REPEATING = 1;
    const NOT_REPEATING = 0;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'template_id',
        'client_id',
        'user_id',
        'created_by',
        'category',
        'title',
        'repeating',
        'frequency',
        'deadline',
        'due_at',
        'end_date',
        'active',
        'regenerated',
        'private',
        'version_no',
        'delivered',
        'delivered_read_at',
        'completed_at',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'deadline',
        'due_at',
        'delivered_read_at',
        'end_date',
        'completed_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Scope a query to only include uncompleted tasks.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUncompleted($query)
    {
        return $query->whereNull('completed_at');
    }

    /**
     * Scope a query to only include completed tasks.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }

    /**
     * Scope a query to only include overdue tasks.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOverdue($query)
    {
        return $query->uncompleted()->whereDate('deadline', '<', Carbon::now());
    }

    /**
     * Return a bool value if task overdue or not 
     *
     * @return bool
     */
    public function isOverdue()
    {
        return $this->deadline->lt(Carbon::now());
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopePrioritized($query)
    {
        return $query->select(['tasks.*', DB::raw('any_value(overdue_reasons.priority) as priority'), DB::raw('any_value(ISNULL(priority)) as null_priority')])
            ->leftJoin(DB::raw('(SELECT * FROM task_overdue_reasons JOIN (SELECT MAX(task_overdue_reasons.id) max_id FROM task_overdue_reasons GROUP BY task_id) AS tor1 ON task_overdue_reasons.id = tor1.max_id) as tor'), 'tasks.id', '=', 'tor.task_id')
            ->leftJoin('overdue_reasons', 'overdue_reasons.id', '=', 'tor.reason_id')
            ->groupBy('tasks.id')
            ->orderBy('null_priority', 'asc')
            ->orderBy('priority', 'asc')
            ->orderBy('deadline');
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeInactive($query)
    {
        return $query->where('active', 0);
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeFilterPrivate($query)
    {
        return $query->where(function ($query) {
            $user = request()->user();

            if (! $user->isAdmin()) {
                $query->where('tasks.user_id', $user->id)->orWhere('tasks.private', 0);
            } else {
                $query->where('tasks.private', 0)->orWhere('tasks.private', 1);
            }
        });
    }

    /**
     * Get the appropriate bootstrap modifier class for the state of the task deadline.
     *
     * @return string
     */
    public function deadlineClass()
    {
        // Success if the task is completed
        if ($this->completed_at != null) {
            return 'success';
        }

        // Danger if the task is overdue
        if ($this->deadline->lte(Carbon::now())) {
            return 'danger';
        }

        // Warning if the task is due today
        if ($this->deadline->between(Carbon::now(), Carbon::now()->endOfDay())) {
            return 'warning';
        }

        // Info as default
        return 'info';
    }

    /**
     * @return bool
     */
    public function askForOverdueReason()
    {
        return $this->deadline->lte(Carbon::now()->addDay()->endOfDay());
    }

    /**
     * @return array
     */
    public function dueDateCountDown()
    {
        $label = '';
        $class = '';

        // Set due date from task and calculate days until deadline
        $dueDate = $this->due_at;
        $daysBeforeDeadline = Carbon::now()->diffInDays($dueDate);

        if ($dueDate > Carbon::now()) {
            if ($daysBeforeDeadline <= 1) {
                $hoursCount = Carbon::now()->diffInHours($dueDate);
                $label = $hoursCount . ' hour'.( $hoursCount == 1 ? '' : 's');
                $class = 'warning';
            } elseif ($daysBeforeDeadline <= 7) {
                $label = $daysBeforeDeadline . ' day'.( $daysBeforeDeadline == 1 ? '' : 's');
                $class = 'info';
            }
        } else {
            $class = 'danger';
            if ($daysBeforeDeadline == 0) {
                $hoursCount = Carbon::now()->diffInHours($dueDate);
                $label = $hoursCount . ' hour'.( $hoursCount == 1 ? '' : 's').' ago';
            } else {
                $label = $daysBeforeDeadline . ' day'.( $daysBeforeDeadline == 1 ? '' : 's').' ago';
            }
        }

        return [
            'days' => $daysBeforeDeadline,
            'label' => $label,
            'class' => $class,
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function review()
    {
        return $this->hasOne(Review::class, 'reviewer_task_id');
    }
    /*
     * Get the version on which the task was created (it may NOT be the latest version no.)
     */
    public function version()
    {
        return $this->hasOne(TemplateVersion::class, 'template_id', 'template_id')->where('version_no', $this->version_no);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function processedNotifications()
    {
        return $this->hasMany(ProcessedNotification::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reopenings()
    {
        return $this->hasMany(TaskReopening::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subtasks()
    {
        return $this->hasMany(Subtask::class);
    }

    /**
     * @return $this
     */
    public function activeSubtasks()
    {
        return $this->subtasks()->whereNull('completed_at');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    /**
     * Get the user that owns the task.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Return if the task is completed or not.
     */
    public function isComplete()
    {
        return ! is_null($this->completed_at);
    }

    /**
     * Return if the task is custom or not.
     */
    public function isCustom()
    {
        return is_null($this->template_id);
    }

    /**
     * @return mixed
     */
    public function changesAccepted()
    {
        return DB::table('tasks_user_acceptance')->where('type', 1)->where('user_id', Auth::user()->id)->where('template_id', $this->template->id)->where('version_no', $this->version_no)->exists();
    }

    /**
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function overdueReason()
    {
        return $this->hasOne(TaskOverdueReason::class, 'task_id')->where('active', 1)->latest();
    }

    /**
     * Returns the last overdue reason whatever its state is.
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function lastOverdueReason()
    {
        return $this->hasOne(TaskOverdueReason::class, 'task_id')->latest();
    }

    /**
     * The overdue reasons owned by the task.
     */
    public function taskOverdueReasons()
    {
        return $this->hasMany(TaskOverdueReason::class, 'task_id');
    }

    /**
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function author()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    /**
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function details()
    {
        return $this->hasOne(TaskDetails::class, 'task_id','id');
    }

    /**
     * @return bool
     */
    public function needsReview()
    {
        // You can't review changes without a template
        if (is_null($this->template)) {
            return false;
        }

        // Tasks with subtasks don't need review
        if ($this->subtasks()->count() > 0) {
            return false;
        }

        if ($this->changesAccepted()) {
            return false;
        }

        // If no rule from above applies you should review changes
        return true;
    }

    /**
     * @return bool
     */
    public function isPrivate()
    {
        return (bool) $this->private;
    }

    /**
     * Check if task is reopened
     *
     * @return bool
     */
    public function isReopened()
    {
        return (bool) $this->reopenings()->count();
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeDelivered($query, $delivered = true)
    {
        return $query->where('delivered', $delivered);
    }

    /**
     * @param TemplateNotification $notification
     * @return mixed
     */
    public function getUserNotificationEmail(TemplateNotification $notification)
    {
        if ($notification->user_type == 'client') {
            return $this->client->email();
        }

        return $this->client->{$notification->user_type}->email;
    }

    /**
     * @param TemplateNotification $notification
     * @return null
     */
    public function getUserNotificationPhone(TemplateNotification $notification)
    {
        if ($notification->user_type == 'client') {
            return $this->client->phone();
        }

        if (isset($this->client->{$notification->user_type})) {
            return $this->client->{$notification->user_type}->phone;
        } else {
            return false;
        }
    }
}
