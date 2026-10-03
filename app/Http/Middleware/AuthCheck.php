<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure$next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            if (Auth::user()->status == 'Inactive') {
                Auth::logout();
                return redirect('/login')->withErrors(['email' => 'These credentials do not match our records.']);
            }
        }
        return $next($request);
    }
}
