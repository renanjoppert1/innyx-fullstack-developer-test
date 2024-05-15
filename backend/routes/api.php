<?php

use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);

Route::group([
     'middleware' => 'auth:sanctum'
], function () {
    Route::group([
        'as' => 'products.',
        'prefix' => 'products'
    ], function () {
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
        Route::post('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
    });


    Route::group([
        'as' => 'categories.',
        'prefix' => 'categories'
    ], function () {
        Route::post('/', [\App\Http\Controllers\CategoryController::class, 'store'])->name('store');
        Route::get('/', [\App\Http\Controllers\CategoryController::class, 'index'])->name('index');
        Route::get('/all', [\App\Http\Controllers\CategoryController::class, 'indexAll'])->name('index.all');
        Route::get('/{category}', [\App\Http\Controllers\CategoryController::class, 'show'])->name('show');
        Route::put('/{category}', [\App\Http\Controllers\CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [\App\Http\Controllers\CategoryController::class, 'destroy'])->name('destroy');
    });


    Route::group([
        'as' => 'users.',
        'prefix' => 'users'
    ], function () {
        Route::post('/', [\App\Http\Controllers\UserController::class, 'store'])->name('store');
        Route::get('/', [\App\Http\Controllers\UserController::class, 'index'])->name('index');
        Route::get('/{user}', [\App\Http\Controllers\UserController::class, 'show'])->name('show');
        Route::put('/{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('destroy');
    });
});



