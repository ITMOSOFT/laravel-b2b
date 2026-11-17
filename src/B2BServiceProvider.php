<?php

namespace ITMOSOFT\B2B;

use Illuminate\Support\ServiceProvider;

class B2BServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/b2b.php', 'itmosoft-b2b');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/b2b.php' => config_path('itmosoft-b2b.php'),
        ], 'b2b-config');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
