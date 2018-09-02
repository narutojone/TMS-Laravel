<?php

namespace App\Repositories\TemplateVersion;

use Illuminate\Database\Eloquent\Model;

class TemplateVersion extends Model
{
    protected $table = 'template_versions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'template_id',
        'version_no',
        'created_by',
        'title',
        'description',
    ];
}
