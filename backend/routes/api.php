<?php

use App\Http\Controllers\Api\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/categories', [SiteController::class, 'categories']);
Route::get('/brands', [SiteController::class, 'brands']);
Route::get('/certificates', [SiteController::class, 'certificates']);
Route::get('/site', [SiteController::class, 'site']);
Route::get('/products', [SiteController::class, 'products']);
Route::get('/products/{product}', [SiteController::class, 'product']);
Route::post('/contact', [SiteController::class, 'contact']);
Route::post('/orders', [SiteController::class, 'order']);
