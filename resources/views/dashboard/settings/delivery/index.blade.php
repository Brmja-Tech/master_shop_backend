@extends('dashboard.master', ['title' => __('dashboard.delivery-settings')])
@section('delivery-settings-active', 'active')
@section('settings-open', 'open')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('dashboard.delivery-settings') }}</h4>
                </div>
                <div class="table-responsive">
                    <div class="card-body">
                        @livewire('dashboard.settings.update-delivery-settings')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
