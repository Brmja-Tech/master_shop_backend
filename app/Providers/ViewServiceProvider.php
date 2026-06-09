<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        view()->composer('dashboard.*', function () {


            $adminsCount = Cache::remember('admins_count', now()->addMinutes(60), function () {
                return Admin::count();
            });

            $usersCount = Cache::remember('users_count', now()->addMinutes(60), function () {
                return User::count();
            });


            view()->share([
                'admins_count'     => $adminsCount,
                'users_count'      => $usersCount,
            ]);
        });

        $setting = Cache::remember('setting', now()->addMinutes(60), function () {
            return Setting::first();
        });
        view()->share([
            'setting'        => $setting,
        ]);
    }
}
