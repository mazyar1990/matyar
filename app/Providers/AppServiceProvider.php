<?php

namespace App\Providers;
use Meilisearch\Client;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
public function register(): void
{

    $this->app->singleton(Client::class, function ($app) {
        // This tells Laravel how to build the Meilisearch Client
        // whenever it's needed via dependency injection.
        return new Client(
            config('scout.meilisearch.host'),
            config('scout.meilisearch.key')
        );
    });
}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}