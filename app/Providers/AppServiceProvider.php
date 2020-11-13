<?php

namespace App\Providers;

use App\Components\Auth\AuthHandler;
use App\Components\Auth\ForgotPasswordHandler;
use App\Components\Auth\TokenGenerator\JwtGenerator;
use App\Components\Auth\TokenGenerator\CodeGenerator;
use App\Components\Auth\TokenGenerator\TokenGenerator;
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
            ->give(CodeGenerator::class);
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
