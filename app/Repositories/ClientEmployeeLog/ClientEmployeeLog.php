<?php

namespace App\Repositories\ClientEmployeeLog;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\User\User;
use App\Repositories\Client\Client;

class ClientEmployeeLog extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['client_id', 'user_id', 'rating', 'type', 'assigned_at', 'removed_at'];

    /**
     * @var bool
     */
    public $timestamps = false;
    
    const TYPE_MANAGER  = 'manager';
    const TYPE_EMPLOYEE = 'employee';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getRatingAttribute($value)
    {
        if (is_null($value)) {
            return $this->attributes['removed_at'] ? 'Neutral' : '-';
        }

        if ($value) {
            return 'Positive';
        }

        return 'Negative';
    }
}
