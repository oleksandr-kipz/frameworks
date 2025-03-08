<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\TestController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', [
    TestController::class,
    'test'
]);

Route::get('/api/v1/products', [
    ProductController::class,
    'getProducts'
]);

Route::get('/api/v1/products/{id}', [
    ProductController::class,
    'getProductItem'
]);

Route::post('/api/v1/products', [
    ProductController::class,
    'createProduct'
])->withoutMiddleware([VerifyCsrfToken::class]);

Route::delete('/api/v1/products/{id}', [
    ProductController::class,
    'deleteProduct'
])->withoutMiddleware([VerifyCsrfToken::class]);
