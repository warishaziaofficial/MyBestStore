<?php

use App\Http\Controllers\CompareController;
use App\Http\Controllers\Admin\CourierCompanyController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\OrderDispatchController;
use App\Http\Controllers\Admin\ProductCatalogController;
use App\Http\Controllers\Admin\ShippingRateController;
use App\Http\Controllers\Admin\ShippingZoneController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\CustomerPasswordResetController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::get('/shop', [StorefrontController::class, 'shop'])->name('shop');
Route::get('/categories', [StorefrontController::class, 'categories'])->name('categories');
Route::get('/new-arrivals', [StorefrontController::class, 'newArrivals'])->name('new-arrivals');
Route::get('/blog', [StorefrontController::class, 'blog'])->name('blog');
Route::get('/contact', [StorefrontController::class, 'contact'])->name('contact');

foreach (StaticPageController::pages() as $slug => $meta) {
    Route::get('/'.$slug, fn () => app(StaticPageController::class)->show($slug))->name($slug);
}

Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');
Route::post('/product/{slug}/reviews', [ProductReviewController::class, 'store'])->name('product.reviews.store');

Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::get('/cart/drawer', [CartController::class, 'drawer'])->name('cart.drawer');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
Route::delete('/cart/coupon', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout');
Route::get('/checkout/shipping-quote', [CheckoutController::class, 'shippingQuote'])->name('checkout.shipping-quote');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

Route::get('/order-success/{order_number}', [OrderController::class, 'success'])->name('order.success');
Route::get('/track-order', [OrderTrackingController::class, 'form'])->name('track-order');
Route::post('/track-order', [OrderTrackingController::class, 'lookup'])->name('track-order.lookup');
Route::get('/track-order/{order_number}', [OrderTrackingController::class, 'show'])->name('order.track');
Route::get('/orders/{order_number}/invoice', [OrderController::class, 'invoice'])->name('order.invoice');
Route::get('/orders/{order_number}/invoice/download', [OrderController::class, 'downloadInvoice'])->name('order.invoice.download');

Route::get('/search/products', [SearchController::class, 'products'])->name('search.products');

Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
Route::post('/wishlist/sync', [WishlistController::class, 'sync'])->name('wishlist.sync');
Route::post('/wishlist/remove', [WishlistController::class, 'remove'])->name('wishlist.remove');

Route::get('/compare', [CompareController::class, 'index'])->name('compare');
Route::post('/compare/toggle', [CompareController::class, 'toggle'])->name('compare.toggle');
Route::post('/compare/sync', [CompareController::class, 'sync'])->name('compare.sync');
Route::post('/compare/remove', [CompareController::class, 'remove'])->name('compare.remove');

Route::post('/customer/login', [CustomerAuthController::class, 'login'])->name('customer.login');
Route::post('/customer/register', [CustomerAuthController::class, 'register'])->name('customer.register');
Route::post('/customer/forgot-password', [CustomerAuthController::class, 'forgotPassword'])->name('customer.forgot-password');
Route::get('/reset-password/{token}', [CustomerPasswordResetController::class, 'show'])->name('customer.password.reset');
Route::post('/reset-password/{token}', [CustomerPasswordResetController::class, 'update'])->name('customer.password.update');
Route::post('/customer/track-order', [CustomerAuthController::class, 'trackOrder'])->name('customer.track-order');
Route::post('/customer/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/products', [ProductCatalogController::class, 'index'])->name('products.index');
    Route::get('/products/{slug}/edit', [ProductCatalogController::class, 'edit'])->name('products.edit');
    Route::put('/products/{slug}', [ProductCatalogController::class, 'update'])->name('products.update');

    Route::get('/shipping/zones', [ShippingZoneController::class, 'index'])->name('shipping.zones.index');
    Route::get('/shipping/zones/create', [ShippingZoneController::class, 'create'])->name('shipping.zones.create');
    Route::post('/shipping/zones', [ShippingZoneController::class, 'store'])->name('shipping.zones.store');
    Route::get('/shipping/zones/{zone}/edit', [ShippingZoneController::class, 'edit'])->name('shipping.zones.edit');
    Route::put('/shipping/zones/{zone}', [ShippingZoneController::class, 'update'])->name('shipping.zones.update');
    Route::delete('/shipping/zones/{zone}', [ShippingZoneController::class, 'destroy'])->name('shipping.zones.destroy');

    Route::get('/shipping/rates', [ShippingRateController::class, 'index'])->name('shipping.rates.index');
    Route::get('/shipping/rates/create', [ShippingRateController::class, 'create'])->name('shipping.rates.create');
    Route::post('/shipping/rates', [ShippingRateController::class, 'store'])->name('shipping.rates.store');
    Route::get('/shipping/rates/{rate}/edit', [ShippingRateController::class, 'edit'])->name('shipping.rates.edit');
    Route::put('/shipping/rates/{rate}', [ShippingRateController::class, 'update'])->name('shipping.rates.update');
    Route::delete('/shipping/rates/{rate}', [ShippingRateController::class, 'destroy'])->name('shipping.rates.destroy');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/dispatch', [OrderDispatchController::class, 'index'])->name('orders.dispatch');
    Route::post('/orders/dispatch', [OrderDispatchController::class, 'store'])->name('orders.dispatch.store');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/packing-slip', [AdminOrderController::class, 'packingSlip'])->name('orders.packing-slip');
    Route::get('/orders/{order}/barcode.svg', [OrderDispatchController::class, 'barcode'])->name('orders.barcode');

    Route::get('/couriers', [CourierCompanyController::class, 'index'])->name('couriers.index');
    Route::get('/couriers/create', [CourierCompanyController::class, 'create'])->name('couriers.create');
    Route::post('/couriers', [CourierCompanyController::class, 'store'])->name('couriers.store');
    Route::get('/couriers/{courier}/edit', [CourierCompanyController::class, 'edit'])->name('couriers.edit');
    Route::put('/couriers/{courier}', [CourierCompanyController::class, 'update'])->name('couriers.update');
    Route::delete('/couriers/{courier}', [CourierCompanyController::class, 'destroy'])->name('couriers.destroy');
});
