<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use App\Models\Mobileuser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthenticateToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $bearerToken = $request->header('authorization');
        $token = '';
        if ($bearerToken && Str::start($bearerToken, 'Bearer ')) {
            $token = substr($bearerToken, 7); // Remove 'Bearer ' prefix
            $user = Mobileuser::where('remember_token',$token)->first();
            if ($user) {
                Auth::login($user); // Manually authenticate the user
            }
            return $next($request);
        }else{
            $token = $bearerToken;
            return $next($request);
        }
    }
}
