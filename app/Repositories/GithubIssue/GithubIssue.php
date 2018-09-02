<?php

namespace App\Repositories\GithubIssue;

use App\Repositories\GithubMilestone\GithubMilestone;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\HarvestDevTimeEntry\HarvestDevTimeEntry;

class GithubIssue extends Model
{
    const IS_PULL_REQUEST = 1;
    const NOT_PULL_REQUEST = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'issue_id',
        'issue_number',
        'issue_estimate',
        'issue_title',
        'milestone_id',
        'pull_request',
        'state',
    ];

    protected $appends = [
        'origin_url',
        'tracked',
        'milestone_title',
    ];

    public function harvestTimeEntities()
    {
        return $this->hasMany(HarvestDevTimeEntry::class, 'github_issue', 'issue_number');
    }

    public function milestone()
    {
        return $this->belongsTo(GithubMilestone::class, 'milestone_id', 'id');
    }

    public function scopeMilestone($query, $milestoneId = null)
    {
        if ($milestoneId) {
            return $query->where('milestone_id', $milestoneId);
        }
        return $query;
    }

    public function scopeState($query, $state = null)
    {
        if ($state) {
            return $query->where('state', $state);
        }
        return $query;
    }

    public function getOriginUrlAttribute()
    {
        if ($this->issue_number) {
            return env('GITHUB_REPO_ISSUE_URL_PREFIX') . $this->issue_number;
        }
        return null;
    }

    public function getMilestoneTitleAttribute()
    {
        if ($this->milestone) {
            return $this->milestone->title;
        }
        return null;
    }

    public function getTrackedAttribute()
    {
        if ($this->harvestTimeEntities) {
            return $this->harvestTimeEntities->sum('tracked_time');
        }
        return 0;
    }

    public function getHitRateAttribute()
    {
        if ($this->issue_estimate) {
            return round(($this->tracked / $this->issue_estimate) * 100, 2);
        }
        return 0;
    }
}