<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Forcer HTTPS uniquement en production ou quand on accède via ngrok (pas en local 127.0.0.1)
        $isNgrok = request()->getHost() && str_contains(request()->getHost(), 'ngrok');
        if (env('APP_ENV') !== 'local' || $isNgrok) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
