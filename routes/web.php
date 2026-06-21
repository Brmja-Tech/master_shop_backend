<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\AdminFcmTokenController;

Route::get('/firebase-messaging-sw.js', [AdminFcmTokenController::class, 'serviceWorker']);

Route::get('/', function () {
    return to_route('dashboard.login');
})->name('login');
