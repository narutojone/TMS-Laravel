<?php

namespace App\Repositories\User;

use App\Repositories\Review\Review;
use App\Repositories\ReviewSetting\ReviewSettingInterface;
use App\Repositories\Task\TaskInterface;
use App\Repositories\UserCompletedTask\UserCompletedTask;
use App\Repositories\HarvestMainTimeEntry\HarvestMainTimeEntry;
use App\Repositories\UserWorkload\UserWorkload;
use App\Services\Templates;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use App\Repositories\Information\Information;
use App\Repositories\Task\Task;
use App\Repositories\Client\Client;
use App\Repositories\Flag\Flag;
use App\Repositories\Group\Group;
use App\Repositories\Template\Template;
use App\Repositories\ClientEmployeeLog\ClientEmployeeLog;
use App\Repositories\CustomerType\CustomerType;
use App\Repositories\System\System;
use App\Repositories\TaskType\TaskType;
use App\Repositories\Subtask\Subtask;
use App\Repositories\TaskOverdueReason\TaskOverdueReason;

class User extends Authenticatable
{
    use Notifiable;

    const ROLE_ADMIN = 10;
    const ROLE_CUSTOMER_SERVICE = 20;
    const ROLE_EMPLOYEE = 30;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'country',
        'experience',
        'degree',
        'invoice_percentage',
        'pf_id',
        'phone',
        'password',
        'authorized',
        'yearly_statement_capacity',
        'customer_capacity',
        'updated_profile',
        'role',
        'level',
        'client_id',
        'out_of_office',
        'active',
        'level_increased_at',
        'weekly_capacity',
        'harvest_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = [
        'format_phone_number',
    ];

    public static  $availableRoles = [
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_CUSTOMER_SERVICE => 'Customer service',
        self::ROLE_EMPLOYEE => 'Employee',
    ];

    public function isInReviewerGroup()
    {
        $reviewSettingsRepository = app()->make(ReviewSettingInterface::class);
        $reviewSetting = $reviewSettingsRepository->model()->first();

        // This only happens if we do not have a record in review_settings table
        if (!$reviewSetting) {
            return false;
        }
        $firstGroupUsers = $reviewSetting->firstGroup->users->pluck('id')->toArray();

        if (in_array($this->id, $firstGroupUsers)) {
            return true;
        }

        $secondGroupUsers = $reviewSetting->secondGroup->users->pluck('id')->toArray();
        if (in_array($this->id, $secondGroupUsers)) {
            return true;
        }

        return false;
    }

    /**
     * @return mixed|null
     */
    public function getAvailableRole()
    {
        $roles = self::$availableRoles;

        if (! isset($roles[$this->role])) {
            return null;
        }

        return str_replace(' ', '_', strtolower($roles[$this->role]));
    }

    /**
     * @param $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->role == $role;
    }

    /**
     * Return the query for the clients the user has access to.
     *
     * @return
     */
     public function getAccessibleClientsQuery()
     {
         $clients = DB::table('clients')->where(function($query) {
            $query->where(function($query) {
                 $query->where('manager_id', $this->id);
                 $query->orWhere('employee_id', $this->id);
            });
             $query->where('clients.active', 1);

         })->orWhere(function($query) {
             $tasks = Task::where('user_id', $this->id)->whereNull('completed_at');
             $tasks->where('active',1);
             $query->whereIn('id',  $tasks->pluck('client_id'));
         });

         return DB::table(DB::raw("(({$clients->toSql()}) as clients)"))
             ->mergeBindings($clients);
     }

    /**
     * Return the query for the clients the user has access to.
     *
     * @param bool $onlyActive
     *
     * @return mixed
     */
     public function getOldClientsQuery($onlyActive = true)
     {
         return Client::notContactPerson($this, $onlyActive)->withConnectedCompletedTasks($this, $onlyActive);
     }

    /**
      * Scope a query to only include user who are not administrators.
      *
      * @param  \Illuminate\Database\Eloquent\Builder  $query
      * @return \Illuminate\Database\Eloquent\Builder
      */
     public function scopeNotAdmin($query)
     {
         return $query->where('role', self::ROLE_EMPLOYEE);
     }

    public function scopeCanHaveCustomTask($query, Client $client)
    {
        return $query->where('id', $client->employee_id)
            ->orWhere('id', $client->manager_id)
            ->orWhereIn('id', $client->tasks()->pluck('user_id'));
    }

    /**
      * Scope a query to only include active users.
      *
      * @param  \Illuminate\Database\Eloquent\Builder  $query
      * @return \Illuminate\Database\Eloquent\Builder
      */
     public function scopeActive($query)
     {
         return $query->where('active', true);
     }

    /**
      * Scope a query to only include deactivated users.
      *
      * @param  \Illuminate\Database\Eloquent\Builder  $query
      * @return \Illuminate\Database\Eloquent\Builder
      */
     public function scopeDeactivated($query)
     {
         return $query->where('active', false);
     }

    /**
     * Get the active clients where the user is the employee.
     *
     * @param bool $onlyActive
     *
     * @return $this
     */
    public function clients($onlyActive = true)
    {
        return $this->hasMany('App\Repositories\Client\Client', 'employee_id')
            ->where('internal', 0)
            ->where(function ($query) use ($onlyActive) {
                if ($onlyActive) {
                    return $query->where('clients.active', 1);
                }
        });
    }

    /**
     * Get the all clients where the user is the employee.
     */
    public function allClients()
    {
        return $this->hasMany(Client::class, 'employee_id');
    }

    /**
     * Get the clients the user manages.
     *
     * @param bool $onlyActive
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clientsManaging($onlyActive = true)
    {
        return $this->hasMany(Client::class, 'manager_id')->where(function ($query) use ($onlyActive) {
            if ($onlyActive) {
                return $query->where('clients.active', 1);
            }
        });
    }

    public function internalClient()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * Get the tasks assigned to the user.
     *
     * @param bool $onlyActive
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks($onlyActive = true)
    {
        return $this->hasMany(Task::class)->where(function ($query) use ($onlyActive) {
            if ($onlyActive) {
                return $query->where('tasks.active', 1);
            }
        });
    }

    /**
     * Get the tasks the user manages.
     */
    public function tasksManaging()
    {
        return $this->hasManyThrough(Task::class, Client::class, 'manager_id')->where('clients.active', 1);
    }

    /**
     * The templates that belong to the user.
     */
    public function templates()
    {
        return $this->belongsToMany(Template::class);
    }

    /**
     * Return if the user manages any clients or not.
     */
    public function isManager()
    {
        return ($this->clientsManaging()->count() > 0);
    }

    /**
     * Return if the user has access to a specific client.
     *
     * @param  \App\Repositories\Client\Client $client
     *
     * @return boolean
     */
    public function canAccessClient(Client $client)
    {
        // Check if the user is the client employee
        if ($this->id === $client->employee_id) {
            return true;
        }

        // Check if the user is the client manager
        if ($this->id === $client->manager_id) {
            return true;
        }

        // Check if the user has any tasks under the client
        if ($client->tasks()->where('user_id', $this->id)->count() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Return if the user is able to complete a specific template.
     *
     * @param  \App\Repositories\Template\Template $template
     *
     * @return boolean
     */
    public function canDoTemplate(Template $template)
    {
        return $this->templates()->where('template_id', $template->id)->count() > 0;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function customerTypes()
    {
        return $this->belongsToMany(CustomerType::class, 'user_customer_types', 'user_id', 'customer_type_id');
    }

    public function completedTasksForReviewForCurrentLevel()
    {
        $reviewSettingsRepository = app()->make(ReviewSettingInterface::class);
        $reviewSetting = $reviewSettingsRepository->model()->first();

        return $this
            ->hasMany(UserCompletedTask::class, 'user_id', 'id')
            ->where('user_completed_tasks.user_level', '=', $this->level)
            ->where('user_completed_tasks.review_id', '=', NULL)
            ->orderBy('user_completed_tasks.id', 'ASC')
            ->limit($reviewSetting->no_of_tasks_for_level_two);
    }

    public function taskReviewsApproved()
    {
        $reviewSettingsRepository = app()->make(ReviewSettingInterface::class);
        $reviewSetting = $reviewSettingsRepository->model()->first();

        return $this
            ->hasMany(UserCompletedTask::class, 'user_id', 'id')
            ->where('user_completed_tasks.user_level', '=', $this->level)
            ->orderBy('user_completed_tasks.id', 'ASC')
            ->limit($reviewSetting->no_of_tasks_for_level_two);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function systems()
    {
        return $this->belongsToMany(System::class, 'user_systems', 'user_id', 'system_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function taskTypes()
    {
        return $this->belongsToMany(TaskType::class, 'user_task_types', 'user_id', 'task_type_id');
    }

    /**
     * Get the subtasks assigned to the user.
     */
    public function subtasks()
    {
        return $this->hasMany(Subtask::class);
    }

    /**
     * Get all reviews created for a user.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'user_id');
    }

    public function pendingReview()
    {
        return $this->reviews()->where('status', '=', Review::STATUS_PENDING)->limit(1);
    }

    public function hasReviewForCurrentLevel() {
        return $this->reviews()
            ->where('user_id', '=', $this->id)
            ->where('user_level', '=', $this->level)
            ->limit(1);
    }

    /**
     * Get the task overdue reasons assigned to the user.
     */
    public function taskOverdueReasons()
    {
        return $this->hasMany(TaskOverdueReason::class);
    }

    public function workload(int $numberOfMonths = 4)
    {
        $currentYear = date("Y");
        $currentMonth = date("m");

        return $this->hasMany(UserWorkload::class)->where(function($query) use ($currentYear, $currentMonth){
            $query->where(function($query) use ($currentYear, $currentMonth) {
                $query->where('year', $currentYear);
                $query->where('month', '>=', $currentMonth);
            })
            ->orWhere(function($query) use ($currentYear){
                $query->where('year', '>', $currentYear);
            });

        })
        ->orderBy('year', 'ASC')
        ->orderBy('month', 'ASC')
        ->limit($numberOfMonths);
    }

    /**
    * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function flags()
    {
        return $this->belongsToMany(Flag::class)
            ->withPivot('comment', 'active', 'client_id','expirationDate')
            ->withTimestamps();
    }

    /**
     * @return bool
     */
    public function hasInformationToShow()
    {
        return Information::getForUser($this)->count() ? true : false;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function information()
    {
        return $this->belongsToMany(Information::class)
            ->withPivot('accepted_status')
            ->withTimestamps();
    }

    /**
     * @return null|string
     */
    public function getFormatPhoneNumberAttribute()
    {
        if ($this->phone) {
            return '+'.$this->phone;
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    /**
     * @return bool
     */
    public function isCustomerService()
    {
        return $this->hasRole(self::ROLE_CUSTOMER_SERVICE);
    }

    /**
     * @return bool
     */
    public function isEmployee()
    {
        return $this->hasRole(self::ROLE_EMPLOYEE);
    }

    /**
     * @return bool
     */
    public function isAdminOrCustomerService()
    {
        return $this->isAdmin() || $this->isCustomerService();
    }

    /**
     * @param $group_id
     * @return bool
     */
    public function isGroupMember($group_id)
    {
        $userGroups = $this->groups->pluck('id')->toArray();

        return in_array($group_id, $userGroups);
    }

    /**
     * @return bool
     */
    public function hasFlags()
    {
        return $this->flags()->wherePivot('active', 1)->count();
    }

    /**
     * @return mixed|string
     */
    public function flagColor()
    {
        return $this->lastFlag()->hex;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|mixed|null|static
     */
    public function lastFlag()
    {
        return $this->flags()->wherePivot('active', 1)->orderBy('pivot_created_at', 'desc')->first();
    }

    public function hasOverdueTasks()
    {
        $tasks = DB::table('tasks')
            ->leftJoin('task_overdue_reasons', function($join)
            {
                $join->on('tasks.id', '=', 'task_overdue_reasons.task_id');
                $join->where('task_overdue_reasons.active', 1);
            })
            ->where('tasks.user_id', $this->id)
            ->where('tasks.active', 1)
            ->whereNull('tasks.completed_at')
            ->whereNull('task_overdue_reasons.reason_id')
            ->where('tasks.deadline', '<', Carbon::now())->count();

        if($tasks == 0) return false;
        return true;
    }

    public function overdueTasks()
    {
        $tasks = Task::select(['tasks.*'])
            ->with(['client', 'user','overdueReason.overdueReason', 'subtasks', 'activeSubtasks', 'template'])
            ->leftJoin('task_overdue_reasons', function($join)
            {
                $join->on('tasks.id', '=', 'task_overdue_reasons.task_id');
                $join->where('task_overdue_reasons.active', 1);
            })
            ->whereNull('task_overdue_reasons.id')
            ->where('tasks.active', 1)
            ->where('tasks.user_id', $this->id)
            ->whereNull('tasks.completed_at')
            ->where('tasks.deadline', '<', Carbon::now());

        return $tasks;
    }

    /**
     * Returns the list of tasks that are due in a specific period
     * @param $startDay
     * @param null $endDay
     * @return mixed
     */
    public function getTasksDueAt($startDay, $endDay = null)
    {
        $tasksRepository = app()->make(TaskInterface::class);
        $tasks = $tasksRepository->model()->select(['tasks.*'])
            ->with(['client', 'user', 'overdueReason.overdueReason', 'subtasks', 'activeSubtasks', 'template'])
            ->leftJoin('task_overdue_reasons', function ($join) {
                $join->on('tasks.id', '=', 'task_overdue_reasons.task_id');
                $join->where('task_overdue_reasons.active', 1);
            })
            ->where('tasks.active', 1)
            ->where('tasks.user_id', $this->id)
            ->whereNull('tasks.completed_at');

        if (empty($endDay)) {
            $tasks->where(function ($query) use ($startDay) {
                $query->where(function ($query) use ($startDay) {
                    $query->where('tasks.deadline', '>=', $startDay)
                          ->whereNull('task_overdue_reasons.expired_at');
                })->orWhere('task_overdue_reasons.expired_at', '>=', $startDay);
            });
        } elseif ($startDay == $endDay) {
            $tasks->where(function ($query) use ($startDay) {
                $query->where(function ($query) use ($startDay) {
                    $query->where('tasks.deadline', $startDay)
                          ->whereNull('task_overdue_reasons.expired_at');
                })->orWhere('task_overdue_reasons.expired_at', $startDay);
            });
        } else {
            $tasks->where(function ($query) use ($startDay, $endDay) {
                $query->where(function ($query) use ($startDay, $endDay) {
                    $query->where('tasks.deadline', '>=', $startDay)
                          ->where('tasks.deadline', '<=', $endDay)
                          ->whereNull('task_overdue_reasons.expired_at');
                })->orWhere(function ($query) use ($startDay, $endDay) {
                    $query->orWhere('task_overdue_reasons.expired_at', '>=', $startDay)
                          ->where('task_overdue_reasons.expired_at', '<=', $endDay);
                });
            });

        }

        return $tasks;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clientLogs()
    {
        return $this->hasMany(ClientEmployeeLog::class)->with(['client', 'employee'])->orderBy('assigned_at', 'desc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

    /**
     * @param Template $template
     *
     * @return bool
     */
    public function canProcessTemplate(Template $template)
    {
        if (! in_array($this->id, app(Templates::class)->getAllowedUsersIds($template))) {
            return false;
        }

        return true;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function timeEntries()
    {
        return $this->hasMany(HarvestMainTimeEntry::class);
    }

    /**
     * @return mixed
     */
    public function timeEntriesThisWeek()
    {
        $monday = Carbon::now()->startOfWeek()->format('Y-m-d');
        return $this->timeEntries()->where('spent_date', '>=', $monday)->sum('hours');
    }
}
