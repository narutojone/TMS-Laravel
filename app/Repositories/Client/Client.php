<?php

namespace App\Repositories\Client;

use App\Repositories\ClientEditLog\ClientEditLog;
use App\Repositories\Contract\Contract;
use App\Repositories\Contact\Contact;
use App\Repositories\NotifierLog\NotifierLog;
use App\Repositories\HarvestMainTimeEntry\HarvestMainTimeEntry;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\ClientPhone\ClientPhone;
use App\Repositories\ClientEmployeeLog\ClientEmployeeLog;
use App\Repositories\File\File;
use App\Repositories\System\System;
use App\Repositories\User\User;
use App\Repositories\PhoneSystemLog\PhoneSystemLog;
use App\Repositories\Flag\Flag;
use App\Repositories\Rating\Rating;

class Client extends Model
{
    const IS_ACTIVE = 1;
    const NOT_ACTIVE = 0;

    const TYPE_UNKNOWN = 0;
    const TYPE_AS = 1;
    const TYPE_ENK = 2;

    const IS_PAID = 1;
    const NOT_PAID = 0;

    const IS_INTERNAL = 1;
    const NOT_INTERNAL = 0;

    const IS_PAUSED = 1;
    const NOT_PAUSED = 0;

    const IS_HIGH_RISK = 1;
    const IS_NOT_HIGH_RISK = 0;

    public static  $mvaTypes = [
        self::TYPE_AS => 'AS',
        self::TYPE_ENK => 'ENK',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'manager_id',
        'employee_id',
        'name',
        'organization_number',
        'zendesk_id',
        'system_id',
        'type',
        'country_code',
        'city',
        'address',
        'postal_code',
        'note',
        'paid',
        'active',
        'paused',
        'risk',
        'risk_reason',
        'complaint_case',
        'show_folders',
        'internal',
        'harvest_id',
    ];

    protected $appends = [
        'time_entries_week_average',
    ];

    /**
     * Get the manager that owns the client.
     */
    public function manager()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the employee that owns the client.
     */
    public function employee()
    {
        return $this->belongsTo(User::class);
    }
  
    /**
     * Get the latest assigned employee
     */
    public function latestEmployee()
    {
         return $this->hasOne(ClientEmployeeLog::class, 'client_id')
             ->where('type', ClientEmployeeLog::TYPE_EMPLOYEE)
             ->whereNotNull('user_id')
             ->latest('assigned_at');
    }
    
    /**
     * Get the latest assigned manager
     */
    public function latestManager()
    {
         return $this->hasOne(ClientEmployeeLog::class, 'client_id')
             ->where('type', ClientEmployeeLog::TYPE_MANAGER)
             ->whereNotNull('user_id')
             ->latest('assigned_at');
    }
    
    /**
     * Get the internal user that owns the client.
     */
    public function internalUser()
    {
        return  $this->hasOne(User::class, 'client_id');
    }

    /**
     * Get the contact persons for the client
     */
    public function contacts()
    {
        return $this->belongsToMany(Contact::class)
            ->withPivot('primary')
            ->orderBy('client_contact.primary', 'DESC')
            ->orderBy('name', 'ASC');
    }

    /**
     * Get the tasks for the client.
     *
     * @param bool $onlyActive
     *
     * @return $this|\Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks($onlyActive = true)
    {
        if($onlyActive) {
            return $this->hasMany('App\Repositories\Task\Task')->where('tasks.active',1);
        }

        return $this->hasMany('App\Repositories\Task\Task');
    }

    /**
     * Get the notes owned by tha client.
     */
    public function notes()
    {
        return $this->hasMany('App\Repositories\Note\Note');
    }

    /**
     * Get the notifications sent to the client.
     */
    public function notifications()
    {
        return $this->hasMany(NotifierLog::class);
    }

    /**
     * @param string("employee", "manager") $type
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employeeLogs(string $type = ClientEmployeeLog::TYPE_EMPLOYEE)
    {
        return $this->hasMany(ClientEmployeeLog::class)->with(['client', 'employee'])
            ->where('type', $type)
            ->orderBy('assigned_at', 'desc');
    }

    public function editLogs()
    {
        return $this->hasMany(ClientEditLog::class)->orderBy('created_at', 'DESC');
    }

    /**
     * Get the main phone for a client
     * If client is internal then we use the employee phone number
     * If client is NOT internal then we use the phone number from the main contact person
     *
     * @return string|null
     */
    public function phone()
    {
        if($this->internal) {
            if($this->internalUser) {
                return $this->internalUser->phone;
            }
        }
        else {
            // Fetch phone based on primary contact person
            if($clientContact = $this->contacts->first()) {
                $mainContactPhone = $clientContact->phones->first();
                if($mainContactPhone) {
                    return $mainContactPhone->number;
                }
            }
        }

        return null;
    }

    /**
     * Get the main email address for a client
     * If client is internal then we use the employee email address
     * If client is NOT internal then we use the email address from the main contact person
     *
     * @return string|null
     */
    public function email()
    {
        if($this->internal) {
            if($this->internalUser) {
                return $this->internalUser->email;
            }
        }
        else {
            // Fetch phone based on primary contact person
            if($clientContact = $this->contacts->first()) {
                $mainContactEmail = $clientContact->emails->first();
                if($mainContactEmail) {
                    return $mainContactEmail->address;
                }
            }
        }

        return null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files()
    {
        return $this->hasMany(File::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contracts()
    {
        return $this->hasMany(Contract::class)->orderBy('id', 'DESC');
    }

    /**
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function filesLatest()
    {
        return $this->files()->latest();
    }

    /**
     * Get client ratings
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function ratings()
    {
        return $this->morphMany(Rating::class, 'commentable');
    }

    /**
     * Get client ratings where client is ratingable
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function ratingsRatingable()
    {
        return $this->morphMany(Rating::class, 'ratingable');
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('clients.active', 1);
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeInactive($query)
    {
        return $query->where('clients.active', 0);
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopePaused($query, $paused = true)
    {
        return $query->where('paused', $paused);
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeInternal($query, $type = true)
    {
        return $query->where('internal', $type);
    }

    /**
     * @param $query
     * @param \App\Repositories\User\User $user
     * @param $onlyActive
     *
     * @return mixed
     */
    public function scopeNotContactPerson($query, User $user, $onlyActive)
    {
        $query = $query->where(function ($builder) use ($user) {
            $builder->where('manager_id', '<>', $user->id)
                ->where('employee_id', '<>', $user->id)
                ->orWhereNull('manager_id')
                ->orWhereNull('employee_id');
        });

        return $onlyActive ? $query->active() : $query;
    }

    /**
     * @param $query
     * @param \App\Repositories\User\User $user
     * @param $onlyActive
     *
     * @return mixed
     */
    public function scopeWithConnectedCompletedTasks($query, User $user, $onlyActive)
    {
        $tasks = $onlyActive  ? $user->tasks() : $user->tasks(false);
        return $query->where(function ($query) use ($tasks) {
            $query->whereIn('id', $tasks->completed()->pluck('client_id')->unique());
        });
    }

    /**
     * @param $query
     * @param \App\Repositories\User\User $user
     * @param $onlyActive
     * @return mixed
     */
    public function scopeWithConnectedUncompletedTasks($query, User $user, $onlyActive)
    {
        $tasks = $onlyActive  ? $user->tasks() : $user->tasks(false);

        return $query->where(function ($query) use ($tasks) {
            $query->whereIn('id', $tasks->uncompleted()->pluck('client_id'));
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function phoneSystemLogs()
    {
        return $this->hasMany(PhoneSystemLog::class);
    }

    /**
     * @param int $paginate
     *
     * @return mixed
     */
    public function phoneSystemLogsHasEmployeePaginated($paginate = 30)
    {
        return  $this->phoneSystemLogs()->hasEmployee()->paginate($paginate);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function system()
    {
        return $this->belongsTo(System::class);
    }

    /**
    * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function flags()
    {
        return $this->belongsToMany(Flag::class, 'flag_user', 'client_id')
            ->withPivot('comment', 'active', 'user_id','expirationDate')
            ->withTimestamps();
    }

    public function hasFlags()
    {
        return $this->flags()->count();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function timeEntries()
    {
        return $this->hasMany(HarvestMainTimeEntry::class, 'client_id', 'id');
    }


    /**
     * @return float|int
     */
    public function getTimeEntriesWeekAverageAttribute()
    {
        $timePoint = Carbon::now()->subDays(28)->setTime(0,0);
        $hours = $this->timeEntries()->where('spent_date', '>=', $timePoint)->sum('hours');
        return round($hours / 4,2);
    }

}
