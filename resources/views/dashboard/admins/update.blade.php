@extends('dashboard.master', ['title' => 'Admin Update'])
@section('admins-active', 'active')
@section('admins-open', 'open')
@section('content')
    <section id="basic-horizontal-layouts">
        <div class="row">
            <div class="col-md-12 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('dashboard.update-admin') }}</h4>
                    </div>
                    <div class="card-body">
                        <form class="form form-horizontal" method="POST" action="{{ route('dashboard.admins.update' , $admin->id) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" value="{{$admin->id}}">
                            <div class="row">
                                <div class="col-6">
                                    <div class="col-sm-3">
                                        <label class="col-form-label">{{ __('dashboard.name') }}</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="name"
                                            value="{{$admin->name}}">
                                    </div>
                                    @error('name')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-6">
                                    <div class="col-sm-3">
                                        <label class="col-form-label">{{ __('dashboard.email') }}</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="email" class="form-control" name="email"
                                            value="{{$admin->email}}">
                                    </div>
                                    @error('email')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-6">
                                    <div class="col-sm-3">
                                        <label class="col-form-label">{{ __('dashboard.password') }}</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="password" class="form-control" name="password"
                                            placeholder="{{ __('dashboard.password') }}">
                                    </div>
                                    @error('password')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <div class="col-sm-3">
                                        <label class="col-form-label">{{ __('dashboard.password-confirmation') }}</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="password" class="form-control" name="password_confirmation"
                                            placeholder="{{ __('dashboard.password-confirmation') }}">
                                    </div>
                                    @error('password_confirmation')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                    <div class="mb-1 col-6">
                                        <div class="col-sm-3">
                                            <label class="form-label" for="basicSelect1">{{__('dashboard.status')}}</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-select" name="status" id="basicSelect1">
                                                <option @selected($admin->status == 1) value="1">{{__('dashboard.active')}}</option>
                                                <option @selected($admin->status == 0) value="0">{{__('dashboard.inactive')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-1 col-6">
                                        <div class="col-sm-3">
                                            <label class="form-label" for="basicSelect">{{__('dashboard.role')}}</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-select" name="role_id" id="basicSelect">
                                                <option value="" selected>{{__('dashboard.select-role')}}</option>
                                                @foreach ($roles as $role)
                                                <option @selected($role->id == $admin->role_id) value="{{$role->id}}">{{$role->role}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('role_id')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                    </div>

                                <div class=" mt-2 col-sm-9 offset-sm-3">
                                    <button type="submit"
                                        class="btn btn-primary me-1 waves-effect waves-float waves-light">{{ __('dashboard.submit') }}</button>
                                    <button type="reset"
                                        class="btn btn-outline-secondary waves-effect">{{ __('dashboard.reset') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
