<?php

namespace App\Providers;

use Sentinel;
use Illuminate\Support\ServiceProvider;

class CustomSentinelProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Sentinel::getUserRepository()->setModel('App\User');
        Sentinel::getRoleRepository()->setModel('App\Role');
    }
}
