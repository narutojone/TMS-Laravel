<?php

namespace App\Repositories\Flag;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\User\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class Flag extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = ['reason', 'client_specific', 'client_removal', 'sms', 'days', 'hex'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('comment', 'active')
            ->withTimestamps();
    }

    /**
     * @return string
     */
    public function validTo()
    {
        if (! $this->days) {
            return 'Endless';
        }

        return $this->pivot->created_at->addDays($this->days)->format('Y-m-d');
    }

    /**
     * @return bool
     */
    public function isEndless()
    {
        return ! $this->days ? true : false;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->pivot->active == 1;
    }
}
