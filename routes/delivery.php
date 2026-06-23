<?php

use App\Http\Controllers\Api\Delivery\Auth\DeliveryAuthController;
use App\Http\Controllers\Api\Delivery\OrderController;
use App\Http\Controllers\Api\Delivery\ProfileController;
use App\Http\Controllers\Api\Delivery\WalletController;
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
        Route::get('orders/current-accepted', [OrderController::class, 'currentAccepted']);
        Route::get('orders/completed', [OrderController::class, 'completed']);
        Route::post('orders/{order}/accept', [OrderController::class, 'accept']);
        Route::post('orders/{order}/complete', [OrderController::class, 'complete']);
        Route::post('orders/{order}/reject', [OrderController::class, 'reject']);

        // Wallet & Withdrawals
        Route::get('wallet/orders-with-withdraw-status', [WalletController::class, 'ordersWithWithdrawStatus']);
        Route::get('wallet/withdrawable-order', [WalletController::class, 'withdrawableOrders']);
        Route::get('wallet/withdraw-requests', [WalletController::class, 'withdrawalRequests']);
        Route::post('wallet/withdraw-requests', [WalletController::class, 'storeWithdrawalRequest']);
    });
});
