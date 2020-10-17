<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use App\Components\FirebaseStorageAdapter;

class FirebaseStorageProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(FirebaseStorageAdapter::class, function ($app) {
            $firebaseCredentials = [
                "type" => env('FIREBASE_TYPE', ''),
                "project_id" => env('FIREBASE_PROJECT_ID', ''),
                "private_key_id" => env('FIREBASE_PRIVATE_KEY_ID', ''),
                "private_key" => preg_replace('/\\n/', '\n', env('FIREBASE_PRIVATE_KEY', '')),
                "client_email" => env('FIREBASE_CLIENT_EMAIL', ''),
                "client_id" => env('FIREBASE_CLIENT_ID', ''),
                "auth_uri" => env('FIREBASE_AUTH_URI', ''),
                "token_uri" => env('FIREBASE_TOKEN_URI', ''),
                "auth_provider_x509_cert_url" => env('FIREBASE_AUTH_PROVIDER_X509_CERT_URL', ''),
                "client_x509_cert_url" => env('FIREBASE_CLIENT_X509_CERT_URL', '')
            ];
            $storage = (new Factory)->withServiceAccount($firebaseCredentials)->createStorage();
            return new FirebaseStorageAdapter($storage);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
