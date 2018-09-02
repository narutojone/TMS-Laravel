<?php

namespace App\Repositories\EmailTemplate;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'active',
        'name',
        'template_file',
        'title',
        'content',
        'content_html',
        'footer',
        'footer_html',
        'folder_id',
    ];

    /**
     * @param $query
     * @param $value
     */
    public function scopeTemplate($query, $value)
    {
        return $query->where('name', $value);
    }
}
