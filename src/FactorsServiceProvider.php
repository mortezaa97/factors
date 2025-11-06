<?php

namespace Mortezaa97\Factors;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Mortezaa97\Factors\Models\Factor;
use Mortezaa97\Factors\Models\FactorHasItem;
use Mortezaa97\Factors\Policies\FactorPolicy;
use Mortezaa97\Factors\Policies\FactorHasItemPolicy;

class FactorsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        // Load views from package
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'factors');
        
        // Load migrations from package
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        
        // Load routes from package
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        // Register policies
        Gate::policy(Factor::class, FactorPolicy::class);
        Gate::policy(FactorHasItem::class, FactorHasItemPolicy::class);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('factors.php'),
            ], 'config');

            // Publishing the views.
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/factors'),
            ], 'views');

            // Publishing migrations.
            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'migrations');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'factors');

        // Register the main class to use with the facade
        $this->app->singleton('factors', function () {
            return new Factors;
        });
    }
}

