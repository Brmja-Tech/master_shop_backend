@extends('dashboard.master', ['title' => 'Vendor Profile'])
@section('vendors-active', 'active')
@section('content')

    <section class="app-user-view-account">
        <div class="row">
            <!-- Vendor Sidebar -->
            <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
                <!-- Vendor Card -->
                <div class="card">
                    <div class="card-body">
                        <div class="user-avatar-section">
                            <div class="d-flex align-items-center flex-column">
                                @if ($vendor->logo)
                                    <img class="img-fluid rounded mt-3 mb-2" src="{{ url($vendor->logo) }}" height="110"
                                        width="110" alt="Vendor Logo" />
                                @else
                                    <div class="avatar bg-light-secondary rounded mt-3 mb-2" style="width: 110px; height: 110px; display: flex; align-items: center; justify-content: center;">
                                        <span class="fs-1">N/A</span>
                                    </div>
                                @endif
                                <div class="user-info text-center">
                                    <h4>{{ $vendor->store_name }}</h4>
                                    <span class="badge bg-light-secondary">{{ $vendor->storeType?->name }}</span>
                                </div>
                            </div>
                        </div>

                        <h4 class="fw-bolder border-bottom pb-50 mb-1 mt-2">{{ __('dashboard.details-for') }} {{ $vendor->store_name }}</h4>
                        <div class="info-container">
                            <ul class="list-unstyled">
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">{{ __('dashboard.store-name') }}:</span>
                                    <span>{{ $vendor->store_name }}</span>
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">{{ __('dashboard.owner-name') }}:</span>
                                    <span>{{ $vendor->owner_name }}</span>
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">{{ __('dashboard.phone') }}:</span>
                                    <span>{{ $vendor->phone }}</span>
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">{{ __('dashboard.address-description') }}:</span>
                                    <span>{{ $vendor->address_description ?? '--' }}</span>
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">{{ __('dashboard.verified') }}:</span>
                                    <span class="badge bg-light-{{ $vendor->is_verified ? 'success' : 'danger' }}">
                                        {{ $vendor->is_verified ? __('dashboard.yes') : __('dashboard.no') }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Vendor Card -->
            </div>
            <!--/ Vendor Sidebar -->

            <!-- Vendor Content -->
            <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
                <!-- Products table -->
                <div class="card">
                    <h4 class="card-header">{{ __('dashboard.products') }}</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('dashboard.image') }}</th>
                                    <th>{{ __('dashboard.name') }}</th>
                                    <th>{{ __('dashboard.price') }}</th>
                                    <th>{{ __('dashboard.discount-precentage') }}</th>
                                    <th>{{ __('dashboard.qty') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($vendor->products as $product)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @php
                                                $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();
                                            @endphp
                                            @if ($mainImage)
                                                <img src="{{ url($mainImage->image) }}" alt="image" width="50" class="rounded">
                                            @else
                                                <div class="avatar bg-light-secondary rounded">
                                                    <div class="avatar-content">N/A</div>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->price }}</td>
                                        <td>{{ $product->discount }}%</td>
                                        <td>{{ $product->quantity }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <div class="text-danger text-center">{{ __('dashboard.no-data') }}</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /Products table -->
            </div>
            <!--/ Vendor Content -->
        </div>
    </section>

@endsection
