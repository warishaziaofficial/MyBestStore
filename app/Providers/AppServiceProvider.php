<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('components.header', function ($view) {
            $view->with([
                'navigation' => config('storefront.navigation', []),
                'megaMenu' => config('storefront.mega_menu', []),
            ]);
        });
    }
}
