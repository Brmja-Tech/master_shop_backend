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
                                    <span class="fw-bolder me-25">{{ __('dashboard.approval_status') }}:</span>
                                    @if ($vendor->approval_status === 'approved')
                                        <span class="badge bg-light-success">{{ __('dashboard.approved') }}</span>
                                    @elseif ($vendor->approval_status === 'rejected')
                                        <span class="badge bg-light-danger">{{ __('dashboard.rejected') }}</span>
                                    @else
                                        <span class="badge bg-light-warning">{{ __('dashboard.pending') }}</span>
                                    @endif
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">{{ __('dashboard.active_status') }}:</span>
                                    <span class="badge bg-light-{{ $vendor->is_active ? 'success' : 'secondary' }}">
                                        {{ $vendor->is_active ? __('dashboard.active') : __('dashboard.inactive') }}
                                    </span>
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">{{ __('dashboard.ban_status') }}:</span>
                                    <span class="badge bg-light-{{ $vendor->ban ? 'danger' : 'success' }}">
                                        {{ $vendor->ban ? __('dashboard.inactive') : __('dashboard.active') }}
                                    </span>
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">{{ __('dashboard.work-from') }}:</span>
                                    <span>{{ $vendor->work_from ? \Carbon\Carbon::parse($vendor->work_from)->format('H:i') : '--' }}</span>
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">{{ __('dashboard.work-to') }}:</span>
                                    <span>{{ $vendor->work_to ? \Carbon\Carbon::parse($vendor->work_to)->format('H:i') : '--' }}</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Approval & Rejection Actions -->
                        <div class="d-flex justify-content-center gap-1 mt-2 pt-1 border-top">
                            <form action="{{ route('dashboard.vendors.status', ['id' => $vendor->id, 'status' => 'approved']) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success waves-effect waves-float waves-light">
                                    <i class="fa-solid fa-check me-25"></i> {{ __('dashboard.approve') }}
                                </button>
                            </form>
                            <form action="{{ route('dashboard.vendors.status', ['id' => $vendor->id, 'status' => 'rejected']) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger waves-effect waves-float waves-light">
                                    <i class="fa-solid fa-xmark me-25"></i> {{ __('dashboard.reject') }}
                                </button>
                            </form>
                        </div>

                        <!-- Ban & Unban Actions -->
                        <div class="d-flex justify-content-center gap-1 mt-1">
                            <form action="{{ route('dashboard.vendors.ban', ['id' => $vendor->id]) }}" method="POST" class="d-inline w-100">
                                @csrf
                                @if ($vendor->ban)
                                    <button type="submit" class="btn btn-outline-success w-100 waves-effect waves-float waves-light">
                                        <i class="fa-solid fa-unlock me-25"></i> إلغاء الحظر (تفعيل الحساب)
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-outline-danger w-100 waves-effect waves-float waves-light">
                                        <i class="fa-solid fa-ban me-25"></i> حظر حساب المتجر
                                    </button>
                                @endif
                            </form>
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
