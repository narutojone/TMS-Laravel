<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class LogoutDeactivatedUsers
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
        if ($request->user()) {
            if ($request->user()->active == false) {
                $email = $request->user()->email;

                Auth::guard('web')->logout();

                if ($request->wantsJson()) {
                    abort(403, 'Your account has been deactivated.');
                } else {
                    return redirect('/login')
                        ->withInput([
                            'email' => $email
                        ])
                        ->withErrors([
                            'email' => 'Your account has been deactivated.',
                        ]);
                }
            }
        }

        return $next($request);
    }
}
