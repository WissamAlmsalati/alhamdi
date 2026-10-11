<?php

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::redirect('/admin', '/admin/dashboard');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::resource('categories', CategoryController::class)->names('admin.categories');
    Route::resource('brands', BrandController::class)->names('admin.brands');
    Route::resource('products', ProductController::class)->names('admin.products');
    Route::resource('contacts', ContactController::class)->only(['index', 'show', 'update', 'destroy'])->names('admin.contacts');
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'update', 'destroy'])->names('admin.orders');
    Route::resource('certificates', CertificateController::class)->except(['show'])->names('admin.certificates');
});
