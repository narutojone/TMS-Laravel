<?php

namespace App\Repositories\FlagUser;

use Illuminate\Database\Eloquent\Model;

class FlagUser extends Model
{
    protected $table = 'flag_user';
    /**
     * @var array
     */
    protected $fillable = [
        'flag_id', 
        'user_id',
        'client_id',
        'comment',
        'active',
        'expirationDate',
        'created_at',
        'updated_at'
    ];
}
