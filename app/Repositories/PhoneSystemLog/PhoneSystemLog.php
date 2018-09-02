<?php

namespace App\Repositories\PhoneSystemLog;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\User\User;
use App\Repositories\Client\Client;
use App\Repositories\Task\Task;

class PhoneSystemLog extends Model
{
    CONST STATUS_BUSY = 'busy';
    CONST STATUS_NO_ANSWER = 'no-answer';
    CONST STATUS_COMPLETED = 'completed';

    /**
     * @var array
     */
    protected $fillable = [
        'call_id',
        'start_time',
        'end_time',
        'call_duration',
        'media_file',
        'media_duration',
        'from',
        'employee_id',
        'client_id',
        'to',
        'task_id',
    ];

    /**
     * @var array
     */
    protected $dates = ['start_time', 'end_time'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function task()
    {
        return $this->hasOne(Task::class, 'id', 'task_id');
    }

    /**
     * @param $query
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function scopeHasEmployee($query)
    {
        return $query->with(['client', 'employee', 'task'])
            ->whereNotNull('employee_id')
            ->orderBy('id', 'DESC');
    }
}
