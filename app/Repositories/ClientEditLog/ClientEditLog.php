<?php

namespace App\Repositories\ClientEditLog;

use App\Repositories\Client\Client;
use App\Repositories\User\User;
use Illuminate\Database\Eloquent\Model;

class ClientEditLog extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'client_id',
        'user_id',
        'starts_at',
        'ends_at',
        'field',
        'value',
        'reminder_sent_at',
        'comment',
    ];

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
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
