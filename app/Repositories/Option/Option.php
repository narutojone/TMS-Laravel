<?php

namespace App\Repositories\Option;

use App\Repositories\EmailTemplate\EmailTemplate;
use App\Repositories\Template\Template;
use App\Repositories\Group\Group;
use App\Repositories\OverdueReason\OverdueReason;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order',
        'key',
        'name',
        'value',
        'description',
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

    public function emailTemplate()
    {
        return $this->belongsTo(EmailTemplate::class, 'value');
    }

    public function overdueReason()
    {
        return $this->belongsTo(OverdueReason::class, 'value');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function template()
    {
        return $this->belongsTo(Template::class, 'value');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(Group::class, 'value');
    }
}
