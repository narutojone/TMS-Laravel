<?php

namespace App\Repositories\RatingRequest;

use Illuminate\Database\Eloquent\Model;

class RatingRequest extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'token',
        'ratingable',
        'commentable',
    ];
}
