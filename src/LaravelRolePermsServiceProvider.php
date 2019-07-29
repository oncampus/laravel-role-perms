<?php
namespace bedoke\LaravelRolePerms;

use Illuminate\Support\ServiceProvider;

class LaravelRolePermsServiceProvider extends ServiceProvider {

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/views', 'LaravelRolePerms');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $this->publishes([
            __DIR__.'/views' => resource_path('views/vendor/LaravelRolePerms')
        ]);

        $this->publishes([
            __DIR__.'/config/role_perms.php' => config_path('role_perms.php'),
        ]);
    }

    public function register()
    {
        $this->app->bind('RolePerms', 'bedoke\LaravelRolePerms\RolePerms');
        $this->mergeConfigFrom(
            __DIR__.'/config/role_perms.php', 'role_perms'
        );
    }

}
