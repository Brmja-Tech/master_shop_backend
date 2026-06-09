@extends('dashboard.auth.partials.auth', ['title' => 'Send email'])

@section('content')
    <form class="auth-login-form mt-2" action="{{ route('dashboard.password.sendOTP') }}" method="POST">
        @csrf
        <div class="mb-1">
            <label class="form-label">{{ __('auth.email') }}</label>
            <input type="email" class="form-control" name="email" placeholder="{{__('auth.enter-email')}}" autofocus />
            @error('email')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary w-100" >{{ __('auth.send') }}</button>
    </form>
@endsection
