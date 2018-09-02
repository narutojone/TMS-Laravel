<?php

namespace App\Repositories\InformationUser;

use App\Filters\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;

class InformationUser extends Model
{
    use Filterable;

    protected $table = 'information_user';

    protected $fillable = ['information_id', 'user_id', 'accepted_status', 'created_at', 'updated_at'];
}
