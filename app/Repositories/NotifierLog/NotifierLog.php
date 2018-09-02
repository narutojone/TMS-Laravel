<?php

namespace App\Repositories\NotifierLog;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\User\User;
use App\Repositories\Client\Client;

class NotifierLog extends Model
{
    /**
     * @var string
     */
    protected $table = 'notifier_logs';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'client_id',
        'to',
        'type',
        'body',
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
