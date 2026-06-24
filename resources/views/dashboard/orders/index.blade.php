@extends('dashboard.master', ['title' => __('dashboard.orders')])

@section('orders-active', 'active')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h4 class="card-title">{{ __('dashboard.orders') }}</h4>
                </div>
                <div class="card-body">
                    @livewire('dashboard.orders.order-data')
                </div>
            </div>
        </div>
    </div>
@endsection
