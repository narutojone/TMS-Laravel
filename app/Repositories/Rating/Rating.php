<?php

namespace App\Repositories\Rating;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'ratingable_id',
        'ratingable_type',
        'commentable_id',
        'commentable_type',
        'rate',
        'feedback',
        'reviewed',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function ratingable()
    {
        return $this->morphTo();
    }
}
