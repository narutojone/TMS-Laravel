<?php

namespace App\Repositories\HarvestMainTimeEntry;

use Illuminate\Database\Eloquent\Model;

class HarvestMainTimeEntry extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'external_id',
        'client_id',
        'harvest_client_id',
        'user_id',
        'harvest_user_id',
        'spent_date',
        'hours',
        'notes',
    ];
}