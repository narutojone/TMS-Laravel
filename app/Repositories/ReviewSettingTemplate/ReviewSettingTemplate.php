<?php

namespace App\Repositories\ReviewSettingTemplate;

use App\Repositories\ReviewSetting\ReviewSetting;
use App\Repositories\Template\Template;
use Illuminate\Database\Eloquent\Model;

class ReviewSettingTemplate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'review_setting_id',
        'template_id',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'completed_at',
        'created_at',
        'updated_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function template()
    {
        return $this->belongsTo(Template::class, 'template_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reviewSetting()
    {
        return $this->belongsTo(ReviewSetting::class, 'review_setting_id');
    }
}
