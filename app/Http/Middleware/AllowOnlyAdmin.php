<?php

namespace App\Http\Middleware;

use App\Repositories\User\User;
use Closure;
use Illuminate\Support\Facades\Auth;

class AllowOnlyAdmin
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
        if (Auth::user()->hasRole(User::ROLE_ADMIN)) {
            return $next($request);
        }

        abort(403);
    }
}
