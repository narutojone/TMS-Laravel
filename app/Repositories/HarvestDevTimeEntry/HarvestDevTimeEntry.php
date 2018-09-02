<?php

namespace App\Repositories\HarvestDevTimeEntry;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\GithubIssue\GithubIssue;

class HarvestDevTimeEntry extends Model
{
    CONST IGNORED = 1;
    CONST NOT_IGNORED = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'external_id',
        'github_issue',
        'username',
        'tracked_time',
        'spent_date',
        'notes',
        'ignored',
    ];

    public function githubIssue()
    {
        return $this->belongsTo(GithubIssue::class, 'github_issue', 'issue_number');
    }

    public function scopeActive($query)
    {
        return $query->where('ignored', self::NOT_IGNORED);
    }
}