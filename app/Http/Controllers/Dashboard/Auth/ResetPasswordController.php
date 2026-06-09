<?php

namespace App\Http\Controllers\Dashboard\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\Admin;
use App\Services\Dashboard\Auth\ResetServic;

class ResetPasswordController extends Controller
{
    protected $resetServic;

    public function __construct(ResetServic $resetServic)
    {
        $this->resetServic = $resetServic;
    } // End __construct

    public function showResetForm($email)
    {
        $admin = Admin::where('email', $email)->where('status', 1)->first();
        if (!$admin) {
            return redirect()->route('dashboard.password.emailForm')->withErrors(['email' => __('auth.inactive-account')]);
        }
        return view('dashboard.auth.reset', ['email' => $email]);
    } // End Method

    public function resetPassword(ResetPasswordRequest $request)
    {
        $email = $request->email;
        $password = $request->password;
        $admin = Admin::where('email', $email)->where('status', 1)->first();
        if (!$admin) {
            return redirect()->route('dashboard.password.emailForm')->withErrors(['email' => __('auth.inactive-account')]);
        }
        if (!$this->resetServic->resetPassword($email, $password)) {
            return redirect()->back()->withErrors(['password' => __('auth.try-again')]);
        }

        return redirect()->route('dashboard.login')->with('success', __('validation.Password-Reset-Successfully'));
    } // End Method
}
