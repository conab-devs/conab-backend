<?php

namespace App\Providers;

use App\Components\AuthHandler;
use App\Components\ForgotPasswordHandler;
use App\Components\TokenGenerator\JwtGenerator;
use App\Components\TokenGenerator\StringGenerator;
use App\Components\TokenGenerator\TokenGenerator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->when(AuthHandler::class)
            ->needs(TokenGenerator::class)
            ->give(JwtGenerator::class);

        $this->app->when(ForgotPasswordHandler::class)
            ->needs(TokenGenerator::class)
            ->give(StringGenerator::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
