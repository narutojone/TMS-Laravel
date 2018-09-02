<?php

namespace App\Repositories\TemplateOverdueReason;

use App\Repositories\OverdueReason\OverdueReason;
use App\Repositories\Template\Template;
use Illuminate\Database\Eloquent\Model;

class TemplateOverdueReason extends Model
{
    const TRIGGER_NONE = 'none';
    const TRIGGER_CONSECUTIVE = 'consecutive';
    const TRIGGER_TOTAL = 'total';

    const ACTION_HIDE_REASON = 'hide_reason';
    const ACTION_PAUSE_CLIENT = 'pause_client';
    const ACTION_DEACTIVATE_CLIENT = 'deactivate_client';
    const ACTION_REMOVE_EMPLOYEE = 'remove_employee';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'template_id',
        'overdue_reason_id',
        'trigger_type',
        'trigger_counter',
        'action',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function overdueReason()
    {
        return $this->hasOne(OverdueReason::class, 'id', 'overdue_reason_id');
    }
}

