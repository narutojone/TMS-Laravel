<?php

namespace App\Repositories\ProcessedNotificationLog;

use App\Repositories\Task\Task;
use App\Repositories\TemplateNotification\TemplateNotification;
use Illuminate\Database\Eloquent\Model;

class ProcessedNotificationLog extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_DECLINED = 'declined';

    const IS_SENT = true;
    const IS_NOT_SENT = false;
    /**
     * @var array
     */
    protected $fillable = [
        'template_notification_id',
        'task_id',
        'data',
        'status',
        'sent',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function templateNotification()
    {
        return $this->belongsTo(TemplateNotification::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Apply scope to get only notifications that are not sent and are approve
     *
     * @param $query
     * @return mixed
     */
    public function scopeToBeSent($query)
    {
        return $query->where('status', '=', self::STATUS_APPROVED)->where('sent', '=', self::IS_NOT_SENT);
    }

}
