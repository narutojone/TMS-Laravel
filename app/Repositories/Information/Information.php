<?php

namespace App\Repositories\Information;

use App\Filters\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\User\User;

class Information extends Model
{
    use Filterable;

    protected $table = 'information';

    protected $fillable = ['title', 'visibility', 'description', 'created_at', 'updated_at'];

    /**
     * @param $value
     */
    public function setVisibilityAttribute($value)
    {
        $this->attributes['visibility'] = implode(',', $value);
    }

    /**
     * @param $value
     *
     * @return array
     */
    public function getVisibilityAttribute($value)
    {
        return explode(',', $value);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('accepted_status')->withTimestamps();
    }

    /**
     * @param \App\Repositories\User\User $user
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getForUser(User $user)
    {
        $user->load('information');
        $userProcessedInformation = $user->information()->pluck('information_id')->toArray();

        return Information::where(function ($query) use ($user) {
            $query->where('visibility', 'like', "%{$user->email}%")
                ->orWhere('visibility', 'like', "%{$user->getAvailableRole()}%");
        })
            ->whereNotIn('id', $userProcessedInformation)
            ->whereDate('created_at', '>=', $user->created_at);
    }
}
