<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        // Register custom Blade directives for role-based access control
        $this->registerRoleDirectives();
    }

    /**
     * Register role-based Blade directives.
     */
    private function registerRoleDirectives(): void
    {
        // @role('superadmin') - Check for specific role
        Blade::if('role', function ($role) {
            return auth()->check() && auth()->user()->hasRole($role);
        });

        // @hasanyrole(['superadmin', 'inventory']) - Check for any of the roles
        Blade::if('hasanyrole', function ($roles) {
            return auth()->check() && auth()->user()->hasAnyRole((array) $roles);
        });

        // @superadmin - Check if user is superadmin
        Blade::if('superadmin', function () {
            return auth()->check() && auth()->user()->isAdmin();
        });

        // @inventory - Check if user can manage inventory
        Blade::if('inventory', function () {
            return auth()->check() && auth()->user()->canManageInventory();
        });

        // @security - Check if user can manage security
        Blade::if('security', function () {
            return auth()->check() && auth()->user()->canManageSecurity();
        });
    }
}
