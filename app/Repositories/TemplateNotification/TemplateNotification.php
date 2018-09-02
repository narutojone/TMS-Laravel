<?php

namespace App\Repositories\TemplateNotification;

use App\Repositories\ProcessedNotification\ProcessedNotification;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Template\Template;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateNotification extends Model
{
    use SoftDeletes;

    const IS_APPROVED = true;
    const IS_NOT_APPROVED = false;

    const TYPE_EMAIL = 'email';
    const TYPE_TEMPLATE = 'template';
    const TYPE_SMS = 'sms';

    const TRUE = 1;
    const FALSE = 0;
    const BOTH = 2;

    const USER_TYPE_CLIENT = "client";
    const USER_TYPE_EMPLOYEE = "employee";

    public static $statusesForSending = [
        self::FALSE => 'False',
        self::TRUE => 'True',
        self::BOTH => 'Both',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'template_id',
        'type',
        'user_type',
        'before',
        'details',
        'paid',
        'completed',
        'delivered',
        'paused',
        'deleted_at'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'details' => 'array',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'deleted_at'
    ];

    /**
     * @var array
     */
    public static $userTypes = [
        'client' => 'Client',
        'employee' => 'Employee',
        'manager' => 'Manager',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function processedTemplates()
    {
        return $this->hasMany(ProcessedNotification::class);
    }

    /**
     * Check if a template notification was processed.
     *
     * @return bool
     */
    public function isProcessed() : bool
    {
        if ($this->processedTemplate) {
            return true;
        }

        return false;
    }
}
