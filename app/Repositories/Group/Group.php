<?php

namespace App\Repositories\Group;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\User\User;
use App\Repositories\Template\Template;

class Group extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'created_at',
        'updated_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function templates()
    {
        return $this->belongsToMany(Template::class);
    }
}
