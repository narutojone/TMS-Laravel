<?php

namespace App\Providers;

use App\Repositories\User\User;
use App\Repositories\Group\Group;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer(['information.create', 'information.edit'], function ($view) {
            $groups = ['admin', 'customer_service', 'employee'];
            $users = User::select('email')->pluck('email')->toArray();
            $visibilityOptionsArray = array_merge($groups, $users);
            $visibilityOptions = '["' . implode('", "', array_merge($groups, $users)) . '"]';

            $view->with('visibilityOptions', $visibilityOptions);
            $view->with('visibilityOptionsArray', $visibilityOptionsArray);
        });

        View::composer(['settings.email-templates.create', 'settings.email-templates.edit'], function ($view) {
            $filesCollection = app('files')->allFiles(base_path('resources/views/layouts/emails'));
            $filesArray = [];
            foreach ($filesCollection as $file) {
                $filesArray[str_replace('.blade.php', '', $file->getFileName())] = ucwords(str_replace(['.blade.php', '-'], ['', ' '], $file->getFileName()));
            }

            $filesArray = array_merge([null => 'Choose template view'], $filesArray);
            $view->with('emailLayouts', $filesArray);
        });

        View::composer(['groups.assign'], function ($view) {
            $view->with('groups', Group::all());
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
