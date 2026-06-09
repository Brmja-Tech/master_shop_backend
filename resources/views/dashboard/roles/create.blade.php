@extends('dashboard.master', ['title' => 'Role Create'])
@section('createRole-active', 'active')
@section('createRole-open', 'open')
@section('content')
    <section id="basic-horizontal-layouts">
        <div class="row">
            <div class="col-md-12 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('dashboard.create-role') }}</h4>
                    </div>
                    <div class="card-body">
                        <form class="form form-horizontal" method="POST" action="{{ route('dashboard.roles.store') }}">
                            @csrf
                            @method('POST')
                            <div class="row">
                                <div class="col-6">
                                    <div class="col-sm-6">
                                        <label class="col-form-label">{{ __('dashboard.role-ar') }}</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="role[ar]"
                                            placeholder="{{ __('dashboard.role-ar') }}">
                                    </div>
                                    @error('role.ar')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-6">
                                    <div class="col-sm-6">
                                        <label class="col-form-label">{{ __('dashboard.role-en') }}</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="role[en]"
                                            placeholder="{{ __('dashboard.role-en') }}">
                                    </div>
                                    @error('role.en')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-2 col-12">
                                    @if (Config::get('app.locale') == 'ar')
                                    @foreach (config('permessions_ar') as $key => $value)
                                        <div class="form-check form-check-inline col-md-2 mb-1">
                                            <input class="form-check-input" name="permession[]" type="checkbox"
                                                id="inlineCheckbox{{ $key }}" value="{{ $key }}">
                                            <label class="form-check-label"
                                                for="inlineCheckbox{{ $key }}">{{ $value }}</label>
                                        </div>
                                    @endforeach
                                    @else
                                    @foreach (config('permessions_en') as $key => $value)
                                        <div class="form-check form-check-inline col-md-2 mb-1">
                                            <input class="form-check-input" name="permession[]" type="checkbox"
                                                id="inlineCheckbox{{ $key }}" value="{{ $key }}">
                                            <label class="form-check-label"
                                                for="inlineCheckbox{{ $key }}">{{ $value }}</label>
                                        </div>
                                    @endforeach
                                    @endif
                                    @error('permession')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class=" mt-2 col-sm-9 offset-sm-3">
                                    <button type="submit"
                                        class="btn btn-primary me-1 waves-effect waves-float waves-light">{{__('dashboard.submit')}}</button>
                                    <button type="reset" class="btn btn-outline-secondary waves-effect">{{__('dashboard.reset')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
