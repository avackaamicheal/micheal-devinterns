<?php

namespace App\Providers;

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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Blade helper for active links in side bar in the header
        // Usage: @activeRoute('grades.*')
        Blade::directive('activeRoute', function ($expression) {
            return "<?php echo request()->routeIs({$expression}) ? 'active' : ''; ?>";
        });


        // second blade helper for tree views in side bar
        // Add a second directive to AppServiceProvider
        Blade::directive('menuOpen', function ($expression) {
            return "<?php echo request()->routeIs({$expression}) ? 'menu-open' : ''; ?>";
        });

        Blade::directive('route', function ($expression) {
            return "<?php echo resolveRoute({$expression}); ?>";
        });
    }



}
