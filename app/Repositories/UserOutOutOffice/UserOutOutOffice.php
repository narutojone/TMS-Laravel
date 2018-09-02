<?php

namespace App\Repositories\UserOutOutOffice;

use Illuminate\Database\Eloquent\Model;

class UserOutOutOffice extends Model
{
    protected $table = 'users_ooo';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'reason_id', 'from_date', 'to_date', 'accepted_tasks'];

    public function reason()
    {
        return $this->hasOne('App\Repositories\OooReason\OooReason', 'id', 'reason_id');
    }

}
