@extends('dashboard.master', ['title' => __('dashboard.order-details')])
@section('users-active', 'active')
@section('users-open', 'open')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h3 class="mb-0">{{ __('dashboard.order-details') }} #{{ $order->id }}</h3>
        <a href="{{ route('dashboard.user.profile', ['id' => $order->user_id]) }}" class="btn btn-outline-secondary">
            <i data-feather="arrow-left" class="me-25"></i> {{ __('dashboard.back') }}
        </a>
    </div>

    <div class="row">
        <!-- Left Column: Order Summary & Customer Info Card -->
        <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
            <!-- Order Card -->
            <div class="card">
                <div class="card-body">
                    <h4 class="fw-bolder border-bottom pb-50 mb-1"><i data-feather="shopping-cart" class="me-25"></i> {{ __('dashboard.order-summary') }}</h4>
                    <div class="info-container">
                        <ul class="list-unstyled">
                            <li class="mb-75">
                                <span class="fw-bolder me-25">{{ __('dashboard.created-at') }}:</span>
                                <span class="text-secondary">{{ $order->created_at }}</span>
                            </li>
                            <li class="mb-75">
                                <span class="fw-bolder me-25">{{ __('dashboard.status') }}:</span>
                                @php
                                    $statusBadge = match ($order->status) {
                                        \App\Enums\OrderStatus::Pending => 'warning',
                                        \App\Enums\OrderStatus::Accepted => 'info',
                                        \App\Enums\OrderStatus::Preparing => 'primary',
                                        \App\Enums\OrderStatus::Ready => 'secondary',
                                        \App\Enums\OrderStatus::OnTheWay => 'info',
                                        \App\Enums\OrderStatus::Delivered => 'success',
                                        \App\Enums\OrderStatus::Cancelled => 'danger',
                                        default => 'light-secondary',
                                    };
                                @endphp
                                <span class="badge bg-light-{{ $statusBadge }}">{{ $order->status?->label() ?? '--' }}</span>
                            </li>
                            <li class="mb-75">
                                <span class="fw-bolder me-25">{{ __('dashboard.payment-method') }}:</span>
                                <span class="badge bg-light-success">{{ $order->payment_method?->label() ?? '--' }}</span>
                            </li>
                            <li class="mb-75">
                                <span class="fw-bolder me-25">{{ __('dashboard.status') }} {{ __('dashboard.payment-method') }}:</span>
                                @php
                                    $payStatusBadge = match ($order->payment_status) {
                                        \App\Enums\PaymentStatus::Pending => 'warning',
                                        \App\Enums\PaymentStatus::Paid => 'success',
                                        \App\Enums\PaymentStatus::Failed => 'danger',
                                        \App\Enums\PaymentStatus::Refunded => 'info',
                                        default => 'light-secondary',
                                    };
                                @endphp
                                <span class="badge bg-light-{{ $payStatusBadge }}">{{ $order->payment_status?->label() ?? '--' }}</span>
                            </li>
                            @if($order->paymob_order_id)
                            <li class="mb-75">
                                <span class="fw-bolder me-25">{{ __('dashboard.paymob_order_id') }}:</span>
                                <span class="text-secondary">{{ $order->paymob_order_id }}</span>
                            </li>
                            @endif
                            @if($order->paymob_transaction_id)
                            <li class="mb-75">
                                <span class="fw-bolder me-25">{{ __('dashboard.paymob_transaction_id') }}:</span>
                                <span class="text-secondary">{{ $order->paymob_transaction_id }}</span>
                            </li>
                            @endif
                            @if($order->delivered_at)
                            <li class="mb-75">
                                <span class="fw-bolder me-25">{{ __('dashboard.processed_at') }}:</span>
                                <span class="text-secondary">{{ $order->delivered_at }}</span>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Customer Card -->
            <div class="card">
                <div class="card-body">
                    <h4 class="fw-bolder border-bottom pb-50 mb-1"><i data-feather="user" class="me-25"></i> {{ __('dashboard.profile-details') }}</h4>
                    <div class="info-container">
                        <ul class="list-unstyled">
                            <li class="mb-75">
                                <span class="fw-bolder me-25">{{ __('dashboard.name') }}:</span>
                                <span>{{ $order->customer_first_name }} {{ $order->customer_last_name }}</span>
                            </li>
                            <li class="mb-75">
                                <span class="fw-bolder me-25">{{ __('dashboard.phone') }}:</span>
                                <span>{{ $order->customer_phone }}</span>
                            </li>
                            <li class="mb-75">
                                <span class="fw-bolder me-25">{{ __('dashboard.site-address') }}:</span>
                                <span class="text-secondary">{{ $order->delivery_address }}</span>
                            </li>
                            @if($order->notes)
                            <li class="mb-75">
                                <span class="fw-bolder me-25">{{ __('dashboard.description') }}:</span>
                                <span class="text-secondary">{{ $order->notes }}</span>
                            </li>
                            @endif
                            @if($order->cancellation_reason)
                            <li class="mb-75">
                                <span class="fw-bolder me-25 text-danger">{{ __('dashboard.confirm_delete_message') }}:</span>
                                <span class="text-danger">{{ $order->cancellation_reason }} (بواسطة {{ $order->cancelled_by }})</span>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Store / Vendor Card -->
            <div class="card">
                <div class="card-body">
                    <h4 class="fw-bolder border-bottom pb-50 mb-1"><i data-feather="archive" class="me-25"></i> {{ __('dashboard.vendor') }}</h4>
                    <div class="info-container">
                        @if($order->vendor)
                        <ul class="list-unstyled">
                            <li class="mb-75">
                                <span class="fw-bolder me-25">{{ __('dashboard.store-name') }}:</span>
                                <span><a href="{{ route('dashboard.vendor.profile', ['id' => $order->vendor_id]) }}">{{ $order->vendor->store_name }}</a></span>
                            </li>
                            <li class="mb-75">
                                <span class="fw-bolder me-25">{{ __('dashboard.owner-name') }}:</span>
                                <span>{{ $order->vendor->owner_name }}</span>
                            </li>
                            <li class="mb-75">
                                <span class="fw-bolder me-25">{{ __('dashboard.phone') }}:</span>
                                <span>{{ $order->vendor->phone }}</span>
                            </li>
                        </ul>
                        @else
                        <span class="text-muted">--</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Delivery Card -->
            <div class="card">
                <div class="card-body">
                    <h4 class="fw-bolder border-bottom pb-50 mb-1"><i data-feather="truck" class="me-25"></i> {{ __('dashboard.deliveries') }}</h4>
                    <div class="info-container">
                        @if($order->delivery)
                        <ul class="list-unstyled">
                            <li class="mb-75">
                                <span class="fw-bolder me-25">{{ __('dashboard.name') }}:</span>
                                <span><a href="{{ route('dashboard.deliveries.show', ['id' => $order->delivery_id]) }}">{{ $order->delivery->name }}</a></span>
                            </li>
                            <li class="mb-75">
                                <span class="fw-bolder me-25">{{ __('dashboard.phone') }}:</span>
                                <span>{{ $order->delivery->phone }}</span>
                            </li>
                        </ul>
                        @else
                        <span class="text-muted">--</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Order Items Breakdown & Billing -->
        <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
            <div class="card">
                <h4 class="card-header border-bottom"><i data-feather="shopping-bag" class="me-25 font-medium-3"></i> {{ __('dashboard.order-items') }}</h4>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('dashboard.product-image') }}</th>
                                <th>{{ __('dashboard.product-name') }}</th>
                                <th>{{ __('dashboard.unit-price') }}</th>
                                <th>{{ __('dashboard.qty') }}</th>
                                <th class="text-end">{{ __('dashboard.total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($order->items as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @php
                                            $mainImage = null;
                                            if ($item->product) {
                                                $mainImage = $item->product->images->where('is_main', true)->first() ?? $item->product->images->first();
                                            }
                                        @endphp
                                        @if ($mainImage)
                                            <img src="{{ url($mainImage->image) }}" alt="product image" width="60" class="rounded border">
                                        @else
                                            <div class="avatar bg-light-secondary rounded border" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                                <div class="avatar-content">N/A</div>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <h6 class="mb-25 fw-bolder">{{ $item->product_name }}</h6>
                                        <small class="text-muted">{{ __('dashboard.product-type') }}: {{ $item->product_unit ?? $item->product?->unit ?? '--' }}</small>
                                    </td>
                                    <td>{{ $item->final_price }}</td>
                                    <td><span class="badge bg-light-primary rounded-pill">{{ $item->quantity }}</span></td>
                                    <td class="text-end fw-bold text-primary">{{ $item->total_price }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-danger py-2">{{ __('dashboard.no-data') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Billing Details Card -->
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-end">
                        <div class="col-md-6 col-12">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td class="fw-bolder">{{ __('dashboard.subtotal') }}:</td>
                                        <td class="text-end">{{ $order->subtotal }}</td>
                                    </tr>
                                    @if((float)$order->discount_amount > 0)
                                    <tr>
                                        <td class="fw-bolder text-success">{{ __('dashboard.discount') }}:</td>
                                        <td class="text-end text-success">-{{ $order->discount_amount }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td class="fw-bolder">{{ __('dashboard.delivery-fee') }}:</td>
                                        <td class="text-end">{{ $order->delivery_fee }}</td>
                                    </tr>
                                    <tr class="border-top">
                                        <td class="fw-bolder fs-5 text-primary">{{ __('dashboard.total') }}:</td>
                                        <td class="text-end fs-5 fw-bolder text-primary">{{ $order->total }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
