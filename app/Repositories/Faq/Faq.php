<?php

namespace App\Repositories\Faq;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\Template\Template;
use App\Repositories\FaqCategory\FaqCategory;

class Faq extends Model
{
    const DIRECTION_DOWN = 'down';
    const DIRECTION_UP = 'up';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'faq';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'faq_category_id',
        'template_id',
        'title',
        'content',
        'visible',
        'active',
        'tasks',
        'order',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'tasks' => 'array'
    ];

    /**
     * Get the faq category that owns the faq.
     */
    public function faqCategory()
    {
        return $this->belongsTo(FaqCategory::class, 'faq_category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    /**
     * @return mixed
     */
    public function tasks()
    {
        if (! $this->template) {
            return collect([]);
        }

        return $this->template->subtasks()->where('active', 1)->orderBy('order')->get();
    }
}
