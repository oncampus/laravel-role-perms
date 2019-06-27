<?php
namespace kevinberg\LaravelRolePerms;

use Illuminate\Support\ServiceProvider;

class LaravelRolePermsServiceProvider extends ServiceProvider {

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/views', 'LaravelRolePerms');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $this->publishes([
            __DIR__.'/views' => resource_path('views/vendor/LaravelRolePerms')
        ]);
    }

    public function register()
    {

    }

}