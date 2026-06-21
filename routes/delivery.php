<?php

use App\Http\Controllers\Api\Delivery\Auth\DeliveryAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('delivery')->name('delivery.')->middleware('setLocale')->group(function () {
    Route::post('register', [DeliveryAuthController::class, 'delivery_regist']);
    Route::post('login', [DeliveryAuthController::class, 'login']);
    Route::post('logout', [DeliveryAuthController::class, 'logout'])->middleware('delivery.auth');
});
