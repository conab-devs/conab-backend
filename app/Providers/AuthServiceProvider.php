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
    }
}
