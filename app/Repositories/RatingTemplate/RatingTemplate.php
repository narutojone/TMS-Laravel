<?php

namespace App\Repositories\RatingTemplate;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\EmailTemplate\EmailTemplate;

class RatingTemplate extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'subject',
        'email_template',
        'tasks_completed',
        'days_from_last_review',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function emailTemplate()
    {
        return $this->belongsTo(EmailTemplate::class, 'email_template');
    }
}
