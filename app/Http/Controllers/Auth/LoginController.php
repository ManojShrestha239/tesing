<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;

    public function redirectTo()
    {
        // if (Auth::check()) {
        //     $role = Auth::user()->user_type;
        //     switch ($role) {
        //         case 'super':
        //             return '/dashboard';
        //             break;
        //         case 'marketing':
        //             return '/dashboard';
        //             break;
        //         case 'dispatch':
        //             return '/order';
        //             break;
        //         case 'account':
        //             return '/collection';
        //             break;

        //         default:
        //             return '/dashboard';
        //             break;
        //     }
        // }
        $authUser = auth()->user();
        // Get the mapping from config
        $permissionRoutes = config('permission_redirects');

        // Loop through the mapping in the order defined
        foreach ($permissionRoutes as $permission => $route) {
            // Check if user has this permission (directly or via role)
            if ($authUser->hasPermissionTo($permission)) {
                return $route;
            }
        }

        // Fallback: redirect to a default route
        return '/dashboard';
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
