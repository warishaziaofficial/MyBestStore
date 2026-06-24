<?php

use Cms\Http\Controllers\AuthController;
use Cms\Http\Controllers\CustomerPasswordController;
use Cms\Http\Controllers\DispatchController;
use Cms\Http\Controllers\DashboardController;
use Cms\Http\Controllers\FooterSettingsController;
use Cms\Http\Controllers\MerchandisingController;
use Cms\Http\Controllers\NotificationController;
use Cms\Http\Controllers\InquiryDetailController;
use Cms\Http\Controllers\ModerationController;
use Cms\Http\Controllers\OrderDetailController;
use Cms\Http\Controllers\OrdersController;
use Cms\Http\Controllers\ProductsController;
use Cms\Http\Controllers\ProfileController;
use Cms\Http\Controllers\SocialIntegrationController;
use Cms\Http\Controllers\ReportsController;
use Cms\Http\Controllers\ResourceController;
use Cms\Support\ResourceRegistry;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('cms.login');
Route::post('/login', [AuthController::class, 'login'])->name('cms.login.submit');
Route::get('/register', [AuthController::class, 'showRegister'])->name('cms.register');
Route::post('/register', [AuthController::class, 'register'])->name('cms.register.submit');

Route::middleware('cms.auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('cms.logout');
    Route::get('/', [DashboardController::class, 'index'])->name('cms.dashboard');
    Route::get('/profile', [ProfileController::class, 'show'])->name('cms.profile');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('cms.profile.password');

    Route::get('/reports', [ReportsController::class, 'index'])->name('cms.reports');
    Route::post('/reports/ai-insights', [ReportsController::class, 'generateAi'])->middleware('cms.role:admin,editor')->name('cms.reports.ai');

    Route::get('/notifications/poll', [NotificationController::class, 'poll'])->name('cms.notifications.poll');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('cms.notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->whereNumber('id')->name('cms.notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('cms.notifications.read-all');

    Route::get('/products', [ProductsController::class, 'index'])->name('cms.products.index');
    Route::get('/orders', [OrdersController::class, 'index'])->name('cms.orders.index');
    Route::get('/dispatch', [DispatchController::class, 'index'])->name('cms.dispatch.queue');
    Route::get('/dispatch/{id}/scan', [DispatchController::class, 'scan'])->whereNumber('id')->name('cms.dispatch.scan');
    Route::post('/dispatch/{id}/scan', [DispatchController::class, 'scanBarcode'])->whereNumber('id')->name('cms.dispatch.scan-barcode');
    Route::post('/dispatch/{id}/items/{itemId}/scan', [DispatchController::class, 'scanItem'])->whereNumber(['id', 'itemId'])->name('cms.dispatch.scan-item');
    Route::get('/dispatch/{id}/ship', [DispatchController::class, 'ship'])->whereNumber('id')->name('cms.dispatch.ship');
    Route::post('/dispatch/{id}/ship', [DispatchController::class, 'confirmShip'])->whereNumber('id')->middleware('cms.role:admin,editor')->name('cms.dispatch.ship.confirm');
    Route::get('/dispatch/barcode.svg', [DispatchController::class, 'barcodeSvg'])->name('cms.dispatch.barcode');
    Route::patch('/orders/{id}/quick-status', [OrdersController::class, 'quickStatus'])->whereNumber('id')->middleware('cms.role:admin,editor')->name('cms.orders.quick-status');
    Route::patch('/orders/{id}/quick-payment', [OrdersController::class, 'quickPaymentStatus'])->whereNumber('id')->middleware('cms.role:admin,editor')->name('cms.orders.quick-payment');

    Route::get('/social', [SocialIntegrationController::class, 'index'])->name('cms.social.index');
    Route::post('/social/{id}/sync', [SocialIntegrationController::class, 'sync'])->whereNumber('id')->middleware('cms.role:admin,editor')->name('cms.social.sync');
    Route::post('/social/test-webhook', [SocialIntegrationController::class, 'testWebhook'])->middleware('cms.role:admin,editor')->name('cms.social.test-webhook');

    Route::get('/orders/{id}', [OrderDetailController::class, 'show'])->whereNumber('id')->name('cms.orders.show');
    Route::get('/orders/{id}/invoice', [OrderDetailController::class, 'invoice'])->whereNumber('id')->name('cms.orders.invoice');
    Route::patch('/orders/{id}/status', [OrderDetailController::class, 'updateStatus'])->whereNumber('id')->middleware('cms.role:admin,editor')->name('cms.orders.update-status');
    Route::post('/orders/{id}/refund', [OrderDetailController::class, 'storeRefund'])->whereNumber('id')->middleware('cms.role:admin,editor')->name('cms.orders.refund');

    Route::post('/moderate/{entity}/{id}/status', [ModerationController::class, 'updateStatus'])
        ->whereIn('entity', ['reviews', 'ratings'])
        ->whereNumber('id')
        ->middleware('cms.role:admin,editor')
        ->name('cms.moderate.status');

    Route::get('/inquiries/{id}', [InquiryDetailController::class, 'show'])->whereNumber('id')->name('cms.inquiries.show');

    Route::get('/merchandising/featured', [MerchandisingController::class, 'featured'])->name('cms.merchandising.featured');
    Route::get('/merchandising/new-arrivals', [MerchandisingController::class, 'newArrivals'])->name('cms.merchandising.new-arrivals');
    Route::post('/merchandising/{type}', [MerchandisingController::class, 'store'])->whereIn('type', ['featured', 'new-arrivals'])->middleware('cms.role:admin,editor')->name('cms.merchandising.store');
    Route::delete('/merchandising/{type}/{id}', [MerchandisingController::class, 'destroy'])->whereIn('type', ['featured', 'new-arrivals'])->whereNumber('id')->middleware('cms.role:admin,editor')->name('cms.merchandising.destroy');

    Route::get('/customers/password-reset', [CustomerPasswordController::class, 'index'])->middleware('cms.role:admin,editor')->name('cms.customers.password-reset');
    Route::post('/customers/password-reset/set', [CustomerPasswordController::class, 'setPassword'])->middleware('cms.role:admin,editor')->name('cms.customers.password-reset.set');

    Route::get('/settings/footer', [FooterSettingsController::class, 'edit'])->name('cms.settings.footer');
    Route::put('/settings/footer', [FooterSettingsController::class, 'update'])->middleware('cms.role:admin,editor')->name('cms.settings.footer.update');

    Route::prefix('manage/{entity}')
        ->where(['entity' => ResourceRegistry::keys()])
        ->name('cms.resource.')
        ->group(function (): void {
            Route::get('/', [ResourceController::class, 'index'])->name('index');
            Route::get('/create', [ResourceController::class, 'create'])->middleware('cms.role:admin,editor')->name('create');
            Route::post('/', [ResourceController::class, 'store'])->middleware('cms.role:admin,editor')->name('store');
            Route::get('/{id}/edit', [ResourceController::class, 'edit'])->middleware('cms.role:admin,editor')->name('edit');
            Route::put('/{id}', [ResourceController::class, 'update'])->middleware('cms.role:admin,editor')->name('update');
            Route::delete('/{id}', [ResourceController::class, 'destroy'])->middleware('cms.role:admin,editor')->name('destroy');
        });
});
