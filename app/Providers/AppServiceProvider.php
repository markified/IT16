<?php

namespace App\Providers;

use App\Helpers\DataMaskingHelper;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
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
        // Force HTTPS in production to ensure all URLs and redirects use HTTPS
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Paginator::useBootstrap();

        // Register custom Blade directives for role-based access control
        $this->registerRoleDirectives();

        // Register data masking Blade directives
        $this->registerDataMaskingDirectives();
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

    /**
     * Register data masking Blade directives.
     */
    private function registerDataMaskingDirectives(): void
    {
        // @maskEmail($email) - Mask email addresses
        Blade::directive('maskEmail', function ($expression) {
            return "<?php echo e(App\\Helpers\\DataMaskingHelper::maskEmail($expression)); ?>";
        });

        // @maskPhone($phone) - Mask phone/contact numbers
        Blade::directive('maskPhone', function ($expression) {
            return "<?php echo e(App\\Helpers\\DataMaskingHelper::maskPhone($expression)); ?>";
        });

        // @maskIp($ip) - Mask IP addresses
        Blade::directive('maskIp', function ($expression) {
            return "<?php echo e(App\\Helpers\\DataMaskingHelper::maskIpAddress($expression)); ?>";
        });

        // @maskName($name) - Mask names
        Blade::directive('maskName', function ($expression) {
            return "<?php echo e(App\\Helpers\\DataMaskingHelper::maskName($expression)); ?>";
        });

        // @maskable($data, 'type') - Toggleable masked field with show/hide button
        Blade::directive('maskable', function ($expression) {
            return "<?php echo App\\Helpers\\DataMaskingHelper::maskableField($expression); ?>";
        });

        // @maskString($string, $visibleStart, $visibleEnd) - Generic string masking
        Blade::directive('maskString', function ($expression) {
            return "<?php echo e(App\\Helpers\\DataMaskingHelper::maskString($expression)); ?>";
        });
    }
}
