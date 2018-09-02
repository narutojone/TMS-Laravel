<?php

namespace App\Providers;

use App\Policies\ClientPolicy;
use App\Policies\ContactPolicy;
use App\Policies\ContractPolicy;
use App\Policies\ReportPolicy;
use App\Policies\ReviewPolicy;
use App\Policies\NotificationApprovalPolicy;
use App\Policies\OptionPolicy;
use App\Policies\SubtaskPolicy;
use App\Policies\SystemPolicy;
use App\Policies\TaskPolicy;

use App\Repositories\Client\Client;
use App\Repositories\Contact\Contact;
use App\Repositories\Contract\Contract;
use App\Repositories\Report\Report;
use App\Repositories\Review\Review;
use App\Repositories\Option\Option;
use App\Repositories\ProcessedNotification\ProcessedNotification;
use App\Repositories\Subtask\Subtask;
use App\Repositories\System\System;
use App\Repositories\Task\Task;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Client::class                   => ClientPolicy::class,
        Contact::class                  => ContactPolicy::class,
        Contract::class                 => ContractPolicy::class,
        Subtask::class                  => SubtaskPolicy::class,
        System::class                   => SystemPolicy::class,
        Task::class                     => TaskPolicy::class,
        Report::class                   => ReportPolicy::class,
        Review::class                   => ReviewPolicy::class,
        ProcessedNotification::class    => NotificationApprovalPolicy::class,
        Option::class                   => OptionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
