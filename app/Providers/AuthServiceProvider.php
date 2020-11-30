<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\User;
use App\Product;

class AuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('manage-cooperative-admin', function (User $user, User $resource = null) {
            if ($user->user_type === 'ADMIN_CONAB') {
                return true;
            }

            if ($resource) {
                return $user->id === $resource->id && $user->cooperative;
            }

            return false;
        });

        Gate::define('admin-conab', function (User $user) {
            return $user->user_type === 'ADMIN_CONAB';
        });

        Gate::define('destroy-user', function (User $user, User $resource) {
            if ($user->user_type === "ADMIN_CONAB"
                && ($resource->user_type === "ADMIN_CONAB"
                || $resource->cooperative)
            ) {
                return true;
            }

            if (! ($user->user_type === "ADMIN_CONAB")
                && ! $resource->cooperative
                && $user->id === $resource->id
            ) {
                return true;
            }
            return false;
        });

        Gate::define('create-product', function (User $user) {
            return $user->cooperative;
        });

        Gate::define('manage-product', function (User $user, Product $resource) {
            return $user->cooperative
               && (int) $resource->cooperative_id === $user->cooperative_id;
        });
    }
}
