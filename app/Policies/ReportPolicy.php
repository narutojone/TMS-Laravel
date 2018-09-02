<?php

namespace App\Policies;

use App\Repositories\Report\Report;
use App\Repositories\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Report $report)
    {
        return true;
    }
}
