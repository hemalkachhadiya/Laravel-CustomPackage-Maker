<?php

namespace Smarttech\Prod\Providers;

use Illuminate\Support\ServiceProvider;

class ProdProviders extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
