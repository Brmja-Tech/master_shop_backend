<?php

use Livewire\Livewire;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\AdminController;
use App\Http\Controllers\Dashboard\AdminFcmTokenController;
use App\Http\Controllers\Dashboard\NotificationController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\SettingsController;
use App\Http\Controllers\Dashboard\DeliveryController;
use App\Http\Controllers\Dashboard\Auth\AuthController;
use App\Http\Controllers\Dashboard\Auth\ForgotController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\Dashboard\Auth\ResetPasswordController;
use App\Http\Controllers\Dashboard\Settings\StoreTypeController;
use App\Http\Controllers\Api\Admin\SubcategoryController as AdminSubcategoryController;
use App\Http\Controllers\Api\Admin\VendorWithdrawalRequestController as AdminVendorWithdrawalRequestController;
use App\Http\Controllers\Api\Admin\DeliveryWithdrawalRequestController as AdminDeliveryWithdrawalRequestController;
use App\Http\Controllers\Api\Admin\VendorController as AdminVendorController;








Route::group([
    'prefix' => LaravelLocalization::setLocale() . '/dashboard',
    'as' => 'dashboard.',
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
], function () {

    Livewire::setUpdateRoute(function ($handle) {
        return Route::post('/livewire/update', $handle);
    });


    ############################### Auth Routes ############################################
    Route::get('login',       [AuthController::class, 'login'])->name('login');
    Route::post('login/post', [AuthController::class, 'loginPost'])->name('login.post');
    Route::post('logout',     [AuthController::class, 'logout'])->name('logout');

    ############################### Forgot Password Routes ############################################
    Route::group(['prefix' => 'password', 'as' => 'password.'], function () {
        Route::get('email',          [ForgotController::class, 'showEmailForm'])->name('email');
        Route::post('email',         [ForgotController::class, 'sendOTP'])->name('sendOTP');
        Route::get('verify/{email}', [ForgotController::class, 'showOtpForm'])->name('showOtpForm');
        Route::post('verify',        [ForgotController::class, 'verifyOtp'])->name('verifyOtp');

        ############################### Reset Password Routes ############################################
        Route::get('reset/{email}',  [ResetPasswordController::class, 'showResetForm'])->name('resetForm');
        Route::post('reset',         [ResetPasswordController::class, 'resetPassword'])->name('reset');
    });

    ############################### Admin Routes ############################################
    Route::group(['middleware' => 'auth:admin'], function () {

        ############################### Auth Routes ############################################
        Route::get('home', [AuthController::class, 'home'])->name('home');
        Route::get('profile', [ProfileController::class, 'profile'])->name('profile');
        Route::get('security', [ProfileController::class, 'security'])->name('security');
        Route::post('profile/update', [ProfileController::class, 'profileUpdate'])->name('profile.update');
        Route::post('profile/update/password', [ProfileController::class, 'profileUpdatePassword'])->name('profile.update.password');

        ############################### Role Routes ############################################
        Route::resource('roles', RoleController::class)->middleware('can:roles');
        ############################### End Role Routes ############################################

        ############################### Admin Routes ############################################
        Route::resource('admins',         AdminController::class)->middleware('can:admins');
        Route::get('admins/{id}/status', [AdminController::class, 'changeStatus'])->middleware('can:admins')->name('admin.changeStatus');
        ############################### End Amin Routes ############################################

        ############################### Users Routes ############################################
        Route::get('users',                  [UserController::class, 'index'])->middleware('can:users')->name('users.index');
        Route::get('user/profile/{id}',      [UserController::class, 'userProfile'])->middleware('can:users')->name('user.profile');
        ############################### End Users Routes #########################################






        ############################### settings Routes ############################################
        Route::get('banners',             [SettingsController::class, 'banners'])->middleware('can:settings')->name('banners');
        Route::get('settings',            [SettingsController::class, 'genralSetting'])->middleware('can:settings')->name('settings');
        Route::get('delivery-settings',   [SettingsController::class, 'deliverySetting'])->middleware('can:settings')->name('delivery.setting');
        Route::get('abouts',              [SettingsController::class, 'aboutSetting'])->middleware('can:settings')->name('about.setting');
        Route::get('faqs',                [SettingsController::class, 'faqs'])->middleware('can:settings')->name('faqs.setting');
        Route::get('privacy',             [SettingsController::class, 'privacy'])->middleware('can:settings')->name('privacy.setting');
        Route::get('terms',               [SettingsController::class, 'terms'])->middleware('can:settings')->name('terms.setting');
        ############################### End settings Routes ############################################

        Route::get('store-types',         [SettingsController::class, 'storeTypes'])->middleware('can:settings')->name('store-types.setting');
        Route::get('subcategories',       [SettingsController::class, 'subcategories'])->middleware('can:settings')->name('subcategories.setting');
        Route::get('vendors',             [SettingsController::class, 'vendors'])->middleware('can:settings')->name('vendors.setting');
        Route::get('vendor-requests',     [SettingsController::class, 'requests'])->middleware('can:settings')->name('vendors.requests');
        Route::get('vendor/profile/{id}', [SettingsController::class, 'vendorProfile'])->middleware('can:settings')->name('vendor.profile');
        Route::post('vendors/{id}/status', [SettingsController::class, 'updateStatus'])->middleware('can:settings')->name('vendors.status');
        Route::post('vendors/{id}/ban',    [SettingsController::class, 'toggleBan'])->middleware('can:settings')->name('vendors.ban');
        Route::get('withdraw-requests',   [SettingsController::class, 'withdrawRequests'])->middleware('can:settings')->name('withdraw-requests.index');

        ############################### Deliveries Routes #######################################
        Route::get('deliveries',             [DeliveryController::class, 'index'])->middleware('can:settings')->name('deliveries.index');
        Route::get('delivery-requests',      [DeliveryController::class, 'requests'])->middleware('can:settings')->name('deliveries.requests');
        Route::get('deliveries/{id}',        [DeliveryController::class, 'show'])->middleware('can:settings')->name('deliveries.show');
        Route::post('deliveries/{id}/status', [DeliveryController::class, 'updateStatus'])->middleware('can:settings')->name('deliveries.status');
        Route::post('deliveries/{id}/ban',    [DeliveryController::class, 'toggleBan'])->middleware('can:settings')->name('deliveries.ban');
        Route::get('delivery-withdraw-requests', [DeliveryController::class, 'withdrawRequests'])->middleware('can:settings')->name('delivery-withdraw-requests.index');

    });

    Route::prefix('admin')
    ->middleware('admin')
    ->group(function () {
        Route::apiResource('store-types', StoreTypeController::class);
    });
});

Route::prefix('admin')->middleware(['setLocale', 'auth:admin'])->group(function () {
    Route::post('fcm-token', [AdminFcmTokenController::class, 'store']);
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::post('notifications/{notificationId}/read', [NotificationController::class, 'markAsRead']);
    Route::get('subcategories/lookup', [AdminSubcategoryController::class, 'lookup']);
    Route::apiResource('subcategories', AdminSubcategoryController::class);
    Route::get('withdraw-requests', [AdminVendorWithdrawalRequestController::class, 'index']);
    Route::post('withdraw-requests/{vendorWithdrawalRequest}/approve', [AdminVendorWithdrawalRequestController::class, 'approve']);
    Route::post('withdraw-requests/{vendorWithdrawalRequest}/reject', [AdminVendorWithdrawalRequestController::class, 'reject']);
    Route::get('delivery-withdraw-requests', [AdminDeliveryWithdrawalRequestController::class, 'index']);
    Route::post('delivery-withdraw-requests/{deliveryWithdrawalRequest}/approve', [AdminDeliveryWithdrawalRequestController::class, 'approve']);
    Route::post('delivery-withdraw-requests/{deliveryWithdrawalRequest}/reject', [AdminDeliveryWithdrawalRequestController::class, 'reject']);
    Route::apiResource('vendors', AdminVendorController::class)->except(['destroy']);
});
