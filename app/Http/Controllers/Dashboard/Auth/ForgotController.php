<?php

namespace App\Http\Controllers\Dashboard\Auth;

use App\Models\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Services\Dashboard\Auth\ForgotService;

class ForgotController extends Controller
{
    protected $forgotService;

    public function __construct(ForgotService $forgotService)
    {
        $this->forgotService = $forgotService;
    } // End __construct


    public function showEmailForm()
    {
        return view('dashboard.auth.email');
    } // End Method


    public function sendOTP(ForgotPasswordRequest $request)
    {
        $admin = Admin::where('email', $request->email)->where('status', 1)->first();
        if (!$admin) {
            return Redirect()->back()->withErrors(['email' => __('auth.inactive-account')]);
        }
        $admin = $this->forgotService->sendOTP($request->email);

        if (!$admin) {
            return Redirect()->back()->withErrors(['email' => __('auth.try-again')]);
        }
        flash()->success(__('auth.otp-sent'));
        return Redirect()->route('dashboard.password.showOtpForm', ['email' => $admin->email]);
    } // End Method


    public function showOtpForm($email)
    {
        $admin = Admin::where('email', $email)->where('status', 1)->first();
        if (!$admin) {
            return Redirect()->route('dashboard.password.emailForm')->withErrors(['email' => __('auth.inactive-account')]);
        }
        return view('dashboard.auth.confirm', ['email' => $email]);
    } // End Method


    public function verifyOtp(ForgotPasswordRequest $request)
    {
        $admin = Admin::where('email', $request->email)->where('status', 1)->first();
        if (!$admin) {
            return Redirect()->route('dashboard.password.emailForm')->withErrors(['email' => __('auth.inactive-account')]);
        }
        $data = $request->only('email', 'token');
        if (!$this->forgotService->verifyOtp($data)) {
            flash()->error(__('auth.otp-not-verified'));
            return Redirect()->back()->withErrors(['token' => __('auth.try-again')]);
        }
        flash()->success(__('auth.otp-verified'));
        return Redirect()->route('dashboard.password.resetForm', ['email' => $request->email]);
    } // End Method
}
