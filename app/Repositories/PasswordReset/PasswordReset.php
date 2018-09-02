<?php

namespace App\Repositories\PasswordReset;

use Illuminate\Database\Eloquent\Model;

/** 
 * THe model for the PasswordReset entity
*/
class PasswordReset extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'token',
        'created_at'
    ];
}