<?php

use App\Http\Controllers\Api\Delivery\Auth\DeliveryAuthController;
use App\Http\Controllers\Api\Delivery\OrderController;
use App\Http\Controllers\Api\Delivery\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('delivery')->name('delivery.')->middleware('setLocale')->group(function () {
    Route::post('register', [DeliveryAuthController::class, 'delivery_regist']);
    Route::post('login', [DeliveryAuthController::class, 'login']);

    Route::middleware('delivery.auth')->group(function () {
        Route::post('logout', [DeliveryAuthController::class, 'logout']);
        Route::get('profile', [ProfileController::class, 'show']);
        Route::post('profile', [ProfileController::class, 'update']);
        Route::post('location', [ProfileController::class, 'updateLocation']);
        Route::get('orders/available', [OrderController::class, 'index']);
        Route::post('orders/{order}/accept', [OrderController::class, 'accept']);
        Route::post('orders/{order}/reject', [OrderController::class, 'reject']);
    });
});
