<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\authController;
use App\Http\Controllers\API\productController;
use App\Http\Controllers\API\cartController;
use App\Http\Controllers\API\categoryController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\FavoriteController;
use App\Http\Middleware\lang;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::prefix('prod')->middleware('auth:sanctum')->group(function () {

Route::get('/products', [productController:: class,'index']);
Route::get('/show/{id}', [productController:: class,'showProduct']);
Route::post('/insert', [productController:: class,'insert']);
Route::post('/update/{id}', [productController:: class,'update']);
Route::delete('/delete/{id}', [productController:: class,'delete']);
});

Route::prefix('auth')->middleware(lang::class)->group(function () {

    Route::post('signup', [authController::class, "signup"])->name('signup');
    Route::post('login', [authController::class, "login"])->name('login');
    Route::post('logout/{id}', [authController::class, "logout"])->name('logout');
});


Route::prefix('category')->middleware('auth:sanctum')->group(function () {

    Route::get('show', [categoryController::class, "showCategory"])->name('showCategory');

});

Route::prefix('cart')->middleware('auth:sanctum')->middleware(lang::class)->group(function () {

    Route::get('show', [cartController::class, "show"])->name('showCart');
    Route::post('addItems', [cartController::class, "addItems"])->name('addItems');
    Route::delete('/remove/{id}', [cartController::class, 'remove']);

});




Route::prefix('orders')->middleware('auth:sanctum')->middleware(lang::class)->group(function () {
    Route::post('/order', [OrderController::class, 'addOrder']); // Create an order
    Route::get('/ordersh', [OrderController::class, 'getOrders']);
});


Route::prefix('fav')->middleware('auth:sanctum')->middleware(lang::class)->group(function () {
    Route::post('/favPost', [FavoriteController::class, 'store']);
    Route::get('/favSh', [FavoriteController::class, 'show'])->middleware(lang::class);
    Route::delete('/favdes/{id}', [FavoriteController::class, 'delete']);
});

