<?php

namespace Whilesmart\LaravelConfiguration;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ConfigurationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishesMigrations([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], ['laravel-configuration', 'laravel-configuration-migrations']);

        if (config('laravel-configuration.register_routes', true)) {
            Route::prefix('api')->group(function () {
                $this->loadRoutesFrom(__DIR__.'/../routes/laravel-configuration.php');
            });
        }

        $this->publishes([
            __DIR__.'/../routes/laravel-configuration.php' => base_path('routes/laravel-configuration.php'),
        ], ['laravel-configuration', 'laravel-configuration-routes', 'laravel-configuration-controllers']);

        $this->publishes([
            __DIR__.'/Http/Controllers' => app_path('Http/Controllers'),
        ], ['laravel-configuration', 'laravel-configuration-controllers']);

        // Publish config
        $this->publishes([
            __DIR__.'/../config/laravel-configuration.php' => config_path('laravel-configuration.php'),
        ], ['laravel-configuration', 'laravel-configuration-controllers']);
    }
}
