<?php

use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::get('/shop', [StorefrontController::class, 'shop'])->name('shop');
Route::get('/categories', [StorefrontController::class, 'categories'])->name('categories');
Route::get('/new-arrivals', [StorefrontController::class, 'newArrivals'])->name('new-arrivals');
Route::get('/blog', [StorefrontController::class, 'blog'])->name('blog');
Route::get('/contact', [StorefrontController::class, 'contact'])->name('contact');
