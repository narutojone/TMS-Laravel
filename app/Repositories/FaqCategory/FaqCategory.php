<?php

namespace App\Repositories\FaqCategory;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\Faq\Faq;

class FaqCategory extends Model
{
    const DIRECTION_DOWN = 'down';
    const DIRECTION_UP = 'up';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'order',
        'visible',
        'active',
    ];

    /**
     * The faq owned by the faq_category.
     */
    public function faq()
    {
        return $this->hasMany(Faq::class, 'faq_category_id');
    }
}
