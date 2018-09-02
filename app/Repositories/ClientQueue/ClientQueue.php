<?php

namespace App\Repositories\ClientQueue;

use Illuminate\Database\Eloquent\Model;

class ClientQueue extends Model
{
    protected $table = 'clients_queue';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'priority',
        'processed',
        'unlock_at'
    ];
}
