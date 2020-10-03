<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('manage-cooperative-admin', function ($user, \App\User $resource = null) {
            if ($user->user_type === 'ADMIN_CONAB') {
                return true;
            }
            if ($resource) {
                return $user->id === $resource->id
                       && $user->cooperative;
            }
            return false;
        });

        Gate::define('admin-conab', function ($user) {
            return $user->user_type === 'ADMIN_CONAB';
        });

        Gate::define('destroy-user', function ($user, \App\User $resource) {
            if ($user->user_type === "ADMIN_CONAB"
                && ($resource->user_type === "ADMIN_CONAB"
                || $resource->cooperative)
            ) {
                return true;
            }

            if (! $user->user_type === "ADMIN_CONAB"
                && ! $resource->cooperative
                && $user->id === $resource->id
            ) {
                return true;
            }
            return false;
        });
    }
}
