<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Implicitly grant "Super-Admin" role all permission checks using can()
        // Gate::before(function ($user, $ability) {
        //     if ($user->hasRole('Super-Admin')) {
        //         return true;
        //     }
        // });

        Gate::before(function ($user, $ability) {
            if (! $user->hasRole('Super-Admin')) {
                return null;
            }

            static $permissions = null;

            if ($permissions === null) {
                $permissions = Permission::query()
                    ->where('guard_name', 'web')
                    ->pluck('name')
                    ->map(fn($n) => strtolower(trim($n)))
                    ->flip()
                    ->all();
            }

            return array_key_exists(strtolower($ability), $permissions)
                ? true
                : null;
        });
    }
}
