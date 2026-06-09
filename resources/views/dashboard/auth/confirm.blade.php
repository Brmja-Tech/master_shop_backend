@extends('dashboard.auth.partials.auth', ['title' => 'Check OTP'])

@section('content')
    <form class="auth-login-form mt-2" action="{{ route('dashboard.password.verifyOtp') }}" method="POST">
        @csrf
        <div class="mb-1">
            <input hidden type="text" class="form-control" value="{{$email}}" name="email" autofocus />
        </div>
        <div class="mb-1">
            <label class="form-label">{{ __('auth.otp-code') }}</label>
            <input type="text" class="form-control" name="token" placeholder="{{ __('auth.otp-code') }}" autofocus />
            @error('token')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary w-100" >{{ __('auth.send') }}</button>
    </form>
@endsection
