<?php

namespace App\Repositories\System;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\User\User;
use App\Repositories\Client\Client;

class System extends Model
{
    const IS_DEFAULT = 1;
    const IS_NOT_DEFAULT = 0;

    /**
     * @var string
     */
    protected $table = 'systems';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'visible',
        'default',
    ];

    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeVisible($query)
    {
        return $query->where('visible', 1);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_systems');
    }
}
