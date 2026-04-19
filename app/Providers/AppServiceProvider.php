<?php

namespace App\Providers;

use App\Services\TenantContext;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // scoped() creates one instance per HTTP request / queue job lifecycle,
        // preventing tenant bleed between concurrent requests in async contexts.
        // (singleton() would share state across requests in Octane / queue workers)
        $this->app->scoped(TenantContext::class);
    }

    public function boot(): void
    {
        //
    }
}
