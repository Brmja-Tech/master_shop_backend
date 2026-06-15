<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Admin\SubcategoryController as AdminSubcategoryController;
use App\Http\Controllers\Api\Auth\ForgotController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymobController;
use App\Http\Controllers\Api\PaymobWebhookController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\SettingsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Vendor\Auth\VendorAuthController;
use App\Http\Controllers\Api\Vendor\Auth\ForgotController as VendorForgotController;
use App\Http\Controllers\Api\Vendor\ProductController;
use App\Http\Controllers\Api\Vendor\ProfileController;
use App\Http\Controllers\Api\Vendor\SubcategoryController;
use App\Http\Controllers\Api\User\CartController;
use App\Http\Controllers\Api\User\FavoriteProductController;
use App\Http\Controllers\Api\User\VendorController as UserVendorController;
use App\Http\Controllers\Dashboard\Settings\StoreTypeController;





## ================== SETTINGS ================== ##
Route::get('/settings',     [SettingsController::class, 'index']);
Route::get('/about-us',     [SettingsController::class, 'about']);
Route::get('/privacy',      [SettingsController::class, 'privacy']);
Route::get('/terms',        [SettingsController::class, 'terms']);
Route::get('/faq',          [SettingsController::class, 'faq']);
Route::post('/contact',     [SettingsController::class, 'contact']);
Route::get('/banners',      [SettingsController::class, 'banners']);
## ================== SETTINGS ================== ##

## ================== LOOKUPS (Mobile) ================== ##
Route::get('/countries',                            [LocationController::class, 'countries']);
Route::get('/countries/{country_id}/governorates',  [LocationController::class, 'governorates']);
Route::get('/products',                             [ProductController::class, 'publicIndex']);
Route::get('/products/available',                   [ProductController::class, 'available']);
Route::get('/search',                               [SearchController::class, 'index']);
Route::get('/search/vendors',                       [SearchController::class, 'vendors']);
## ================== LOOKUPS (Mobile) ================== ##



## ------------------ AUTH ROUTES ------------------ ##
Route::controller(AuthController::class)->group(function () {
    Route::post('/register',       'register');
    Route::post('/verify-otp',     'verifyOtp')->middleware('guest');
    Route::post('/resend-otp',     'resendOtp')->middleware('guest');
    Route::post('/login',          'login')->middleware('guest');
    Route::post('/logout',         'logout')->middleware('auth:sanctum');
    Route::post('/firebase-login', 'firebaseLogin');
});
## ------------------ AUTH ROUTES ------------------ ##

## ------------------ USER FORGOT PASSWORD ------------------ ##
Route::post('/forgot/password',       [ForgotController::class, 'forgotPassword'])->middleware('guest');
Route::post('/forgot/verify-otp',     [ForgotController::class, 'verifyOtp'])->middleware('guest');
Route::post('/forgot/resend-otp',     [ForgotController::class, 'resendOtp'])->middleware('guest');
Route::post('/forgot/reset-password', [ForgotController::class, 'resetPassword'])->middleware('guest');
## ------------------ USER FORGOT PASSWORD ------------------ ##


## ------------------ VENDOR AUTH ROUTES ------------------ ##

Route::prefix('vendor')->middleware('setLocale')->group(function () {
    Route::post('register', [VendorAuthController::class, 'register']);
    Route::post('forgot-password', [VendorForgotController::class, 'forgotPassword']);
    Route::post('login',    [VendorAuthController::class, 'login']);
    Route::post('logout',   [VendorAuthController::class, 'logout'])->middleware('vendor.auth');
    Route::post('verify-otp',      [VendorForgotController::class, 'verifyOtp']);
    Route::post('reset-password',  [VendorForgotController::class, 'resetPassword']);
    Route::post('resend-otp',      [VendorForgotController::class, 'resendOtp']);

    Route::middleware('vendor.auth')->group(function () {
        Route::get('profile', [ProfileController::class, 'show']);
        Route::post('profile/update', [ProfileController::class, 'update']);
        Route::get('subcategories/lookup', [SubcategoryController::class, 'lookup']);
        Route::apiResource('subcategories', SubcategoryController::class);
        Route::post('products/{id}', [ProductController::class, 'update']);
        Route::apiResource('products', ProductController::class);
    });
});
## ------------------ VENDOR AUTH ROUTES ------------------ ##

Route::prefix('user')->middleware('user.auth')->group(function () {

    Route::prefix('store-types')->group(function () {
        Route::get('lookup', [StoreTypeController::class, 'lookup']);
        Route::get('{id}/vendors', [UserVendorController::class, 'byStoreType']);
    });
    Route::post('location', [AuthController::class, 'updateLocation']);
    Route::prefix('vendors')->group(function () {
        Route::get('', [UserVendorController::class, 'index']);
        Route::get('top-rated', [UserVendorController::class, 'topRated']);
        Route::get('{id}/products', [ProductController::class, 'vendorProducts']);
    });
    Route::prefix('products')->group(function () {
        Route::get('{id}', [ProductController::class, 'publicShow']);
        Route::post('{id}/favorite', [FavoriteProductController::class, 'store']);
        Route::delete('{id}/favorite', [FavoriteProductController::class, 'destroy']);
    });
    Route::prefix('cart')->group(function () {
        Route::get('', [CartController::class, 'index']);
        Route::post('items', [CartController::class, 'store']);
        Route::put('items/{id}', [CartController::class, 'update']);
        Route::delete('items/{id}', [CartController::class, 'destroy']);
        Route::delete('clear', [CartController::class, 'clear']);
    });
    Route::prefix('orders')->group(function () {
        Route::post('', [OrderController::class, 'store']);
        Route::get('', [OrderController::class, 'index']);
        Route::get('{order}', [OrderController::class, 'show']);
        Route::post('{order}/cancel', [OrderController::class, 'cancel']);
    });
    Route::get('favorites', [FavoriteProductController::class, 'index']);
});
Route::get('subcategories/lookup', [AdminSubcategoryController::class, 'lookup']);
Route::post('paymob/callback', [PaymobController::class, 'callback']);
Route::match(['get', 'post'], 'paymob/response', [PaymobController::class, 'response']);
Route::post('paymob/webhook', PaymobWebhookController::class);
