<?php

namespace App\Repositories\GroupTemplate;

use Illuminate\Database\Eloquent\Model;

class GroupTemplate extends Model
{
    protected $table = 'group_template';
    /**
     * @var array
     */
    protected $fillable = [
        'group_id',
        'template_id',
    ];

    public $timestamps = false;
}
