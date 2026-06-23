<?php

use Cms\Http\Controllers\Api\AuthApiController;
use Cms\Http\Controllers\Api\DashboardApiController;
use Cms\Http\Controllers\Api\OrderApiController;
use Cms\Http\Controllers\Api\ProductApiController;
use Cms\Http\Controllers\Api\SocialAccountApiController;
use Cms\Http\Controllers\Api\SocialWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthApiController::class, 'login'])->name('cms.api.login');
Route::post('/logout', [AuthApiController::class, 'logout'])->middleware('cms.auth')->name('cms.api.logout');

Route::middleware('cms.auth')->group(function (): void {
    Route::get('/me', [AuthApiController::class, 'me'])->name('cms.api.me');
    Route::get('/dashboard/stats', [DashboardApiController::class, 'stats'])->name('cms.api.dashboard.stats');

    Route::get('/products', [ProductApiController::class, 'index'])->name('cms.api.products.index');
    Route::get('/products/{id}', [ProductApiController::class, 'show'])->whereNumber('id')->name('cms.api.products.show');
    Route::post('/products', [ProductApiController::class, 'store'])->middleware('cms.role:admin,editor')->name('cms.api.products.store');
    Route::match(['put', 'patch'], '/products/{id}', [ProductApiController::class, 'update'])->whereNumber('id')->middleware('cms.role:admin,editor')->name('cms.api.products.update');
    Route::delete('/products/{id}', [ProductApiController::class, 'destroy'])->whereNumber('id')->middleware('cms.role:admin,editor')->name('cms.api.products.destroy');

    Route::get('/orders', [OrderApiController::class, 'index'])->name('cms.api.orders.index');
    Route::get('/orders/{id}', [OrderApiController::class, 'show'])->whereNumber('id')->name('cms.api.orders.show');
    Route::patch('/orders/{id}/status', [OrderApiController::class, 'updateStatus'])->whereNumber('id')->middleware('cms.role:admin,editor')->name('cms.api.orders.status');
    Route::patch('/orders/{id}/payment-status', [OrderApiController::class, 'updatePaymentStatus'])->whereNumber('id')->middleware('cms.role:admin,editor')->name('cms.api.orders.payment-status');
    Route::patch('/orders/{id}/source', [OrderApiController::class, 'updateSource'])->whereNumber('id')->middleware('cms.role:admin,editor')->name('cms.api.orders.source');

    Route::get('/social/accounts', [SocialAccountApiController::class, 'index'])->name('cms.api.social.accounts.index');
    Route::get('/social/accounts/{id}', [SocialAccountApiController::class, 'show'])->whereNumber('id')->name('cms.api.social.accounts.show');
    Route::post('/social/accounts', [SocialAccountApiController::class, 'store'])->middleware('cms.role:admin,editor')->name('cms.api.social.accounts.store');
    Route::match(['put', 'patch'], '/social/accounts/{id}', [SocialAccountApiController::class, 'update'])->whereNumber('id')->middleware('cms.role:admin,editor')->name('cms.api.social.accounts.update');
    Route::delete('/social/accounts/{id}', [SocialAccountApiController::class, 'destroy'])->whereNumber('id')->middleware('cms.role:admin,editor')->name('cms.api.social.accounts.destroy');
    Route::post('/social/accounts/{id}/sync', [SocialAccountApiController::class, 'sync'])->whereNumber('id')->middleware('cms.role:admin,editor')->name('cms.api.social.accounts.sync');
    Route::post('/social/test-webhook', [SocialAccountApiController::class, 'testWebhook'])->middleware('cms.role:admin,editor')->name('cms.api.social.test-webhook');
    Route::get('/social/sync-logs', [SocialWebhookController::class, 'logs'])->name('cms.api.social.sync-logs');
});
