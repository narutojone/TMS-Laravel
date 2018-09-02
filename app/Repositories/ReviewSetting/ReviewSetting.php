<?php

namespace App\Repositories\ReviewSetting;

use App\Repositories\Group\Group;
use App\Repositories\ReviewSettingTemplate\ReviewSettingTemplate;
use App\Repositories\Template\Template;
use App\Repositories\User\User;
use Illuminate\Database\Eloquent\Model;

class ReviewSetting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'no_of_tasks_for_level_two',
        'deadline_offset',
        'review_template_id',
        'first_review_group_id',
        'second_review_group_id',
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
        return $this->belongsTo(Template::class, 'review_template_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function firstGroup()
    {
        return $this->belongsTo(Group::class, 'first_review_group_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function secondGroup()
    {
        return $this->belongsTo(Group::class, 'second_review_group_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function templates()
    {
        return $this->hasMany(ReviewSettingTemplate::class, 'review_setting_id');
    }

}
