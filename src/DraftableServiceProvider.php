<?php

namespace Davidcb\LaravelDraftable;

use Illuminate\Support\ServiceProvider;

class DraftableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->mergeConfigFrom(__DIR__ . '/../config', 'laravel-draftable');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
    }
}
