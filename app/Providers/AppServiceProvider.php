<?php

namespace App\Providers;

use App\Support\StorefrontData;
use App\Services\CompareService;
use App\Services\CartService;
use App\Services\WishlistService;
use Illuminate\Support\Facades\Auth;
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
                'megaMenu' => StorefrontData::megaMenu(),
                'cartCount' => app(CartService::class)->count(),
            ]);
        });

        View::composer(['components.cart-drawer', 'layouts.app'], function ($view) {
            $customer = Auth::guard('customer')->user();

            $view->with([
                'cartSummary' => app(CartService::class)->summary(),
                'cartCount' => app(CartService::class)->count(),
                'wishlistSlugs' => app(WishlistService::class)->slugs(),
                'compareSlugs' => app(CompareService::class)->slugs(),
                'authCustomer' => $customer ? [
                    'name' => $customer->name,
                    'email' => $customer->email,
                ] : null,
            ]);
        });
    }
}
