<?php

namespace App\Http\Middleware;

use App\Repositories\User\User;
use Closure;

class RequestPrerequisite
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user() && ! $request->is('list_information', 'information') && $request->user()->hasInformationToShow()) {
            return redirect()->route('information.list');
        }

        if($request->user() && !$request->is('list_information', 'information')) {
            // check if user has filled in user capacity
            if (!$request->user()->updated_profile && !$request->user()->hasRole(User::ROLE_ADMIN) && !$request->user()->hasRole(User::ROLE_CUSTOMER_SERVICE)) {
                if (strpos($request->route()->getName(), 'settings') === false) {
                    return redirect('/settings')->withInfo('Hei! Før du kan gå videre trenger vi litt informasjon fra deg. Vennligst fyll inn/se over hvor mange kunder du vil ha, hvilke systemer du vil jobbe i og hvilke oppgaver du vil jobbe med. Til slutt klikker du på "Update" knappen.');
                }
            }
            //check if user has filled weekly capacity
            elseif (!$request->user()->weekly_capacity && !$request->user()->hasRole(User::ROLE_ADMIN) && !$request->user()->hasRole(User::ROLE_CUSTOMER_SERVICE)) {
                if (strpos($request->route()->getName(), 'settings') === false) {
                    return redirect('/settings')->withInfo('Hei! Før du kan gå videre trenger vi å vite antall timer du ønsker å jobbe i uken. Trykk så på "Update" knappen.');
                }
            }
            // check if user has overdue tasks
            elseif ( !$request->is('list_information', 'information') && $request->user()->hasOverdueTasks()) {
                if (strpos($request->route()->getName(), 'tasks') === false) {
                    return redirect('/overdue');
                }
            }
        }
        return $next($request);
    }
}
