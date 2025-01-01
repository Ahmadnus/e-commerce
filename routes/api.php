<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\authController;
use App\Http\Controllers\API\productController;
use App\Http\Controllers\API\cartController;
use App\Http\Controllers\CategoryController;



use PHPUnit\Framework\Attributes\Group;

use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\adminController;
use App\Http\Controllers\CitiesController;
use App\Http\Controllers\AdressesController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CouponController;
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
    Route::post('signupAdmin', [authController::class, "siginUpAd"]);
    Route::post('login', [authController::class, "login"])->name('login');
    Route::post('loginAdmin', [authController::class, "loginAd"]);
    Route::post('logout/{id}', [authController::class, "logout"])->name('logout');
});



Route::prefix('cart')->middleware('auth:sanctum')->middleware(lang::class)->group(function () {

    Route::get('show', [cartController::class, "show"])->name('showCart');
    Route::post('addItems', [cartController::class, "addItems"])->name('addItems');
    Route::delete('/remove/{id}', [cartController::class, 'remove']);

});




Route::prefix('orders')->middleware('auth:sanctum')->middleware(lang::class)->group(function () {
    Route::post('/order', [OrderController::class, 'addOrder']);
    Route::post('/update/{id}', [OrderController::class, 'updateOrder']);
    Route::delete('delete/{id}', [OrderController::class, 'deleteOrder']);
    Route::get('/ordersh', [OrderController::class, 'getOrders']);
});


Route::prefix('fav')->middleware('auth:sanctum')->middleware(lang::class)->group(function () {
    Route::post('/favPost', [FavoriteController::class, 'store']);
    Route::get('/favSh', [FavoriteController::class, 'show'])->middleware(lang::class);
    Route::delete('/favdes/{id}', [FavoriteController::class, 'delete']);
});

Route::prefix('address')->middleware('auth:sanctum')->middleware(lang::class)->group(function () {
    Route::post('/addressPost', [AdressesController::class, 'store']);
    Route::post('/addressUpdate/{id}', [AdressesController::class, 'update']);
    Route::get('/addressGet', [AdressesController::class, 'index']);
    Route::delete('/addressdelete/{id}', [AdressesController::class, 'destroy']);


});
Route::prefix('cities')->middleware('auth:sanctum')->middleware(lang::class)->group(function () {
    Route::post('/citiesPost', [CitiesController::class, 'store']);
    Route::post('/citiesupdate/{city}', [CitiesController::class, 'update']);
    Route::get('/citiesget', [CitiesController::class, 'index']);
    Route::delete('/citiesdelete/{city}', [CitiesController::class, 'destroy']);


});
Route::prefix('brands')->middleware('auth:sanctum')->middleware(lang::class)->group(function () {
    Route::post('/brandPost', [BrandController::class, 'store']);
    Route::post('/brandupdate/{brand}', [BrandController::class, 'update']);
    Route::get('/brandget', [BrandController::class, 'index']);
    Route::delete('/branddelete/{brand}', [BrandController::class, 'destroy']);


});

Route::prefix('coupon')->middleware('auth:sanctum')->middleware(lang::class)->group(function () {
    Route::post('/couponPost', [CouponController::class, 'store']);
    Route::post('/couponupdate/{coupon}', [CouponController::class, 'update']);
    Route::get('/couponget', [CouponController::class, 'index']);
    Route::delete('/coupondelete/{coupon}', [CouponController::class, 'destroy']);


});
Route::prefix('cat')->middleware('auth:sanctum')->middleware(lang::class)->group(function () {
    Route::post('/catPost', [CategoryController::class, 'store']);
    Route::post('/catupdate/{cat}', [CategoryController::class, 'update']);
    Route::get('/catget', [CategoryController::class, 'index']);
    Route::delete('/catdelete/{cat}', [CategoryController::class, 'destroy']);


});








