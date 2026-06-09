<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return to_route('dashboard.login');
})->name('login');
