<?php

namespace App\Repositories\GithubMilestone;

use App\Repositories\GithubIssue\GithubIssue;
use Illuminate\Database\Eloquent\Model;

class GithubMilestone extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number',
        'title',
    ];

    public function issues()
    {
        return $this->hasMany(GithubIssue::class, 'milestone_id', 'id');
    }
}