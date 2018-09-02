<?php

namespace App\Providers;

use Aacotroneo\Saml2\Events\Saml2LoginEvent;
use App\Repositories\Information\Information;
use App\Observers\InformationObserver;
use App\Services\Lists;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;
use Laravel\Dusk\DuskServiceProvider;
use App\Frequency;
use App\Repositories\TemplateSubtask\TemplateSubtask;
use App\Repositories\User\User;
use App\Repositories\Contract\Contract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Custom blade directive for displaying dates
        Blade::directive('date', function ($expression) {
            return "<?php echo (new Carbon\Carbon($expression))->format('j. F Y'); ?>";
        });

        // Custom blade directive for displaying datetimes
        Blade::directive('datetime', function ($expression) {
            return "<?php echo (new Carbon\Carbon($expression))->format('j. F Y H:i'); ?>";
        });

        // Custom blade directive for displaying repeat frequencies
        Blade::directive('frequency', function ($expression) {
            return "<?php echo (new App\Frequency($expression))->display(); ?>";
        });

        // Custom blade directive for displaying text with line breaks
        Blade::directive('nl2br', function ($expression) {
            return "<?php echo nl2br(e($expression)); ?>";
        });

        Blade::directive('superAdmin', function () {
            return "<?php if (auth()->user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN)) : ?>";
        });

        Blade::directive('admin', function () {
            return "<?php if (auth()->user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN) || auth()->user()->hasRole(\App\Repositories\User\User::ROLE_CUSTOMER_SERVICE)) : ?>";
        });

        // Generate an API token on user creation
        User::creating(function ($user) {
            $user->api_token = str_random(60);
        });

        // Add frequency validation rule
        Validator::extend('frequency', function ($attribute, $value, $paramters, $validator) {
            return (new Frequency($value))->isValid();
        });

        Validator::extend('contract_frequency', function ($attribute, $value, $paramters, $validator) {
            $requestData = $validator->getData();
            if($requestData['mva'] == 0 || ($requestData['mva'] == 1 && $requestData['mva_type'] == Contract::MVA_TYPE_YEARLY)) {
                return true;
            }
            else {
                $frequencyNth = (new Frequency($value))->getNth();
                if(in_array($frequencyNth, [1,2])) {
                    return true;
                }
            }
            return false;
        });

        // Add user information visibility rule
        Validator::extend('fit_for_visibility', function ($attribute, $value, $parameters, $validator) {
            $visibilityOptions = array_merge(
                ['admin', 'customer_service', 'employee'],
                $users = User::select('email')->pluck('email')->toArray()
            );

            if (count(array_intersect($value, $visibilityOptions))) {
                return true;
            }

            return false;
        });

        Information::observe(InformationObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register the Laravel Dusk server provider
        if ($this->app->environment('local', 'testing')) {
            $this->app->register(DuskServiceProvider::class);
        }

        $this->app->bind('lists', function () {
            return new Lists();
        });
        
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        $this->app->register('App\Providers\ValidatorsServiceProvider');
        $this->registerRepositories();
    }

    /**  
     * This method is only for registering repositories
    */
    public function registerRepositories()
    {
        // Please add here any repository that is new
        // In this way we are able to register all repositoriy's componenets
        $repositories = [
            'Example',
            'Client',
            'ClientEmployeeLog',
            'ClientEditLog',
            'ClientPhone',
            'ClientQueue',
            'Comment',
            'Contact',
            'ContactEmail',
            'ContactPhone',
            'Contract',
            'ContractSalaryDay',
            'CustomerType',
            'DocumentationPage',
            'EmailTemplate',
            'FailedJob',
            'Faq',
            'FaqCategory',
            'File',
            'Flag',
            'FlagUser',
            'FolderTemplate',
            'GeneratedProcessedNotification',
            'GithubIssue',
            'GithubMilestone',
            'Group',
            'GroupTemplate',
            'GroupUser',
            'HarvestDevTimeEntry',
            'HarvestMainTimeEntry',
            'Information',
            'InformationUser',
            'Invitation',
            'Job',
            'LibraryFile',
            'LibraryFolder',
            'LogSubtaskUserAcceptance',
            'LogTaskUserAcceptance',
            'MatchingQueue',
            'Note',
            'NotifierLog',
            'OooReason',
            'Option',
            'OverdueReason',
            'PasswordReset',
            'PhoneSystemLog',
            'ProcessedNotification',
            'ProcessedNotificationLog',
            'Rating',
            'RatingRequest',
            'RatingTemplate',
            'Review',
            'ReviewSetting',
            'ReviewSettingTemplate',
            'Subtask',
            'SubtaskFileTemplate',
            'SubtaskModuleTemplate',
            'SubtaskReopening',
            'System',
            'SystemSettingGroup',
            'SystemSettingValue',
            'Task',
            'TaskDetails',
            'TaskOverdueReason',
            'TaskReopening',
            'TasksUserAcceptance',
            'TaskType',
            'Template',
            'TemplateFolder',
            'TemplateNotification',
            'TemplateOverdueReason',
            'TemplateSubtask',
            'TemplateSubtaskModule',
            'TemplateSubtaskVersion',
            'TemplateVersion',
            'User',
            'UserCompletedSubtask',
            'UserCompletedTask',
            'UserCustomerType',    
            'UserOutOutOffice',
            'UserSystem',
            'UserTaskType',
            'UserWorkload',
            'ZendeskGroup',
            'ZendeskUser'
        ];

        foreach ($repositories as $repository) {
            $this->app->bind("App\\Repositories\\{$repository}\\{$repository}Interface", function ($app) use ($repository) {

                $eloquentRepository = "App\\Repositories\\{$repository}\\EloquentRepository{$repository}";
                $eloquentModel = "App\\Repositories\\{$repository}\\{$repository}";
                $createValidator = "App\\Repositories\\{$repository}\\{$repository}CreateValidator";
                $updateValidator = "App\\Repositories\\{$repository}\\{$repository}UpdateValidator";

                $repo = new $eloquentRepository(new $eloquentModel);

                $repo->registerValidator('create', new $createValidator($app['validator']));
                $repo->registerValidator('preview', new $createValidator($app['validator']));
                $repo->registerValidator('update', new $updateValidator($app['validator']));

                return $repo;
            });

        }
    }    
}
