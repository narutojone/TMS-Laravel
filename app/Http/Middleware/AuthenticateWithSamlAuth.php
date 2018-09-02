<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Session;
use Aacotroneo\Saml2\Saml2Auth;

class AuthenticateWithSamlAuth
{

    protected $saml2Auth;

    /**
     * @param Saml2Auth $saml2Auth injected.
     */
    function __construct(Saml2Auth $saml2Auth)
    {
        $this->saml2Auth = $saml2Auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        // Check if we are on production
        if(env('APP_ENV', 'production') == 'production') {
            // Check if user is not authenticated
            if(! Auth::guard($guard)->check()){
                $this->saml2Auth->login(Session::get('url.intended'));
            }
        }

        return $next($request);
    }
}
