@extends('dashboard.master', ['title' => 'User Profile'])
@section('users-active', 'active')
@section('users-open', 'open')

@section('content')

    <section class="app-user-view-account">
        <div class="row">
            <!-- User Sidebar -->
            <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
                <!-- User Card -->
                <div class="card">
                    <div class="card-body">
                        <div class="user-avatar-section">
                            <div class="d-flex align-items-center flex-column">
                                <img class="img-fluid rounded mt-3 mb-2" src="{{ asset($user->image) }}" height="110"
                                    width="110" alt="User avatar" />
                                <div class="user-info text-center">
                                    <h4>{{ $user->name }}</h4>
                                    <span class="badge bg-light-secondary">user</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-around my-2 pt-75">
                            {{-- <div class="d-flex align-items-start me-2">
                                <span class="badge bg-light-primary p-75 rounded">
                                    <i data-feather="check" class="font-medium-2"></i>
                                </span>
                                <div class="ms-75">
                                    <h4 class="mb-0">{{ $user->orders->count() }}</h4>
                                    <small>{{ __('dashboard.orders-done') }}</small>
                                </div>
                            </div> --}}
                            {{-- <div class="d-flex align-items-start">
                                <span class="badge bg-light-primary p-75 rounded">
                                    <i data-feather="heart" class="font-medium-2"></i>
                                </span>
                                <div class="ms-75">
                                    <h4 class="mb-0">{{ $user->wishlists->count() }}</h4>
                                    <small>{{ __('dashboard.wishlist') }}</small>
                                </div>
                            </div> --}}
                        </div>
                        <h4 class="fw-bolder border-bottom pb-50 mb-1">{{ __('dashboard.details-for') }} {{ $user->name }}
                        </h4>
                        <div class="info-container">
                            <ul class="list-unstyled">
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">{{ __('dashboard.name') }}:</span>
                                    <span>{{ $user->name }}</span>
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">{{ __('dashboard.email') }}:</span>
                                    <span>{{ $user->email }}</span>
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">{{ __('dashboard.phone') }}:</span>
                                    <span>{{ $user->phone }}</span>
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">{{ __('dashboard.status') }}:</span>
                                    <span
                                        class="badge bg-light-{{ $user->status == 1 ? 'success' : 'danger' }}">{{ $user->status == 1 ? __('dashboard.active') : __('dashboard.inactive') }}</span>
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">{{ __('dashboard.verified') }}:</span>
                                    <span
                                        class="badge bg-light-{{ $user->email_verified_at != null ? 'success' : 'danger' }}">{{ $user->email_verified_at != null ? __('dashboard.active') : __('dashboard.inactive') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /User Card -->
            </div>
            <!--/ User Sidebar -->

            <!-- User Content -->
            <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
                <!-- User Pills -->
                <ul class="nav nav-pills mb-2">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('dashboard.user.profile', ['id' => $user->id]) }}">
                            <i data-feather="user" class="font-medium-3 me-50"></i>
                            <span class="fw-bold">{{ __('dashboard.account') }}</span></a>
                    </li>
                    {{-- <li class="nav-item">
                    <a class="nav-link" href="app-user-view-security.html">
                        <i data-feather="lock" class="font-medium-3 me-50"></i>
                        <span class="fw-bold">Security</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="app-user-view-billing.html">
                        <i data-feather="bookmark" class="font-medium-3 me-50"></i>
                        <span class="fw-bold">Billing & Plans</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="app-user-view-notifications.html">
                        <i data-feather="bell" class="font-medium-3 me-50"></i><span class="fw-bold">Notifications</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="app-user-view-connections.html">
                        <i data-feather="link" class="font-medium-3 me-50"></i><span class="fw-bold">Connections</span>
                    </a>
                </li> --}}
                </ul>
                <!--/ User Pills -->

                <!-- Invoice table -->
                <div class="card">
                    <h4 class="card-header">{{ __('dashboard.user-orders') }}</h4>
                    <div class="table-responsive">
                        <table class="table datatable-project">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>{{ __('dashboard.created-at') }}</th>
                                    <th>{{ __('dashboard.phone') }}</th>
                                    <th>{{ __('dashboard.total-price') }}</th>
                                    <th>{{ __('dashboard.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @foreach ($user->orders as $item)
                                    <tr>
                                        <td>#</td>
                                        <td>{{ $item->created_at }}</td>
                                        <td>{{ $item->user_phone }}</td>
                                        <td>{{ $item->total_price }}</td>
                                        <td>{{ $item->status }}</td>
                                    </tr>
                                @endforeach --}}
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /Invoice table -->

                <!-- Project table -->
                <div class="card">
                    <h4 class="card-header">{{ __('dashboard.user-wishlist') }}</h4>
                    <div class="table-responsive">
                        <table class="table datatable-project">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>{{ __('dashboard.image') }}</th>
                                    <th class="text-nowrap">{{ __('dashboard.name') }}</th>
                                    <th>{{ __('dashboard.qty') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @foreach ($user->wishlists as $item)
                                    <tr>
                                        <td>#</td>
                                        <td><img src="{{ asset($item->product->image) }}" alt="image" width="50">
                                        </td>
                                        <td>{{ $item->product->name }}</td>
                                        <td>{{ $item->product->qty }}</td>
                                    </tr>
                                @endforeach --}}
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /Project table -->


            </div>
            <!--/ User Content -->
        </div>
    </section>

@endsection
