<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserManagement
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
        if(Auth::check()){
            if(Auth::user()->app=='TINT'){
                Auth::logout();
                Session::flash('error_message', 'Credentials do not match our records.');
                return redirect('/');
                }else{
                    return $next($request);
                }
            }else{
                return redirect('/');
            }
    }
}
