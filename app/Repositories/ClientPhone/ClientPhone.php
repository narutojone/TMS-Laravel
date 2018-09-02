<?php

namespace App\Repositories\ClientPhone;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\Client\Client;

class ClientPhone extends Model
{
    protected $fillable = ['client_id', 'number'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
