<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Pagination using Bootstrap-style if needed
        // \Illuminate\Pagination\Paginator::useBootstrap();
    }
}
