<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            @php
                $setting = \App\Models\Setting::first();
                $dashboardLogo = asset('uploads/images/logo.jpeg');
                $vendorsCount = \App\Models\Vendor::count();
                $approvedVendorsCount = \App\Models\Vendor::query()
                    ->where(function ($query) {
                        $query->where('is_verified', true)
                            ->orWhere('approval_status', 'approved');
                    })
                    ->count();
                $pendingVendorRequestsCount = \App\Models\Vendor::query()
                    ->where('is_verified', false)
                    ->whereIn('approval_status', ['pending', 'rejected'])
                    ->count();
                $pendingVendorWithdrawRequestsCount = \App\Models\VendorWithdrawalRequest::query()
                    ->where('status', \App\Enums\VendorWithdrawalStatus::Pending)
                    ->count();
                $pendingDeliveryWithdrawRequestsCount = \App\Models\DeliveryWithdrawalRequest::query()
                    ->where('status', \App\Enums\DeliveryWithdrawalStatus::Pending)
                    ->count();
            @endphp
            <li class="nav-item me-auto"><a class="navbar-brand" href="{{ route('dashboard.home') }}"><span
                        class="brand-logo"><img src="{{ $dashboardLogo }}" alt="Dashboard Logo"></span>
                    <h2 class="brand-text">{{ $setting->site_name }}</h2>
                </a></li>
            <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pe-0" data-bs-toggle="collapse"><i
                        class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i><i
                        class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary" data-feather="disc"
                        data-ticon="disc"></i></a></li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="nav-item @yield('dashboard-active')"><a class="d-flex align-items-center"
                    href="{{ route('dashboard.home') }}"><i data-feather="home"></i><span
                        class="menu-title text-truncate" data-i18n="Email">{{ __('dashboard.home') }}</span></a>
            </li>
            <li class=" navigation-header"><span data-i18n="Apps &amp; Pages">Apps &amp; Pages</span><i
                    data-feather="more-horizontal"></i>
            </li>

            @can('roles')
                <li class="nav-item @yield('roles-open') @yield('createRole-open')"><a class="d-flex align-items-center"
                        href="#"><i data-feather='align-justify'></i><span class="menu-title text-truncate"
                            data-i18n="Roles &amp; Permission">{{ __('dashboard.roles') }}</span></a>
                    <ul class="menu-content">
                        <li><a class="@yield('roles-active') d-flex align-items-center"
                                href="{{ route('dashboard.roles.index') }}"><i data-feather="circle"></i><span
                                    class="menu-item text-truncate" data-i18n="Roles">{{ __('dashboard.roles') }}</span></a>
                        </li>
                        <li><a class="@yield('createRole-active') d-flex align-items-center"
                                href="{{ route('dashboard.roles.create') }}"><i data-feather="circle"></i><span
                                    class="menu-item text-truncate"
                                    data-i18n="Permission">{{ __('dashboard.create-role') }}</span></a>
                        </li>
                    </ul>
                </li>
            @endcan

            @can('admins')
                <li class="nav-item @yield('admins-open') @yield('createAdmin-open')"><a class="d-flex align-items-center"
                        href="#"><i data-feather='users'></i><span class="menu-title text-truncate">
                            {{ __('dashboard.admins') }}</span>
                        <span
                            class="badge badge-light-warning rounded-pill ms-auto me-1">{{ App\Models\Admin::count() }}</span>
                    </a>
                    <ul class="menu-content">
                        <li><a class="@yield('admins-active') d-flex align-items-center"
                                href="{{ route('dashboard.admins.index') }}"><i data-feather="circle"></i><span
                                    class="menu-item text-truncate"
                                    data-i18n="Roles">{{ __('dashboard.admins') }}</span></a>
                        </li>
                        <li><a class="@yield('createAdmin-active') d-flex align-items-center"
                                href="{{ route('dashboard.admins.create') }}"><i data-feather="circle"></i><span
                                    class="menu-item text-truncate"
                                    data-i18n="Permission">{{ __('dashboard.create-admin') }}</span></a>
                        </li>
                    </ul>
                </li>
            @endcan

            @can('users')
                <li class="nav-item @yield('users-open') @yield('createUser-open')"><a class="d-flex align-items-center"
                        href="#"><i data-feather='users'></i><span class="menu-title text-truncate">
                            {{ __('dashboard.users') }}</span>
                        <span class="badge badge-light-warning rounded-pill ms-auto me-1"> {{ App\Models\User::count() }}
                        </span>
                    </a>
                    <ul class="menu-content">
                        <li><a class="@yield('users-active') d-flex align-items-center"
                                href="{{ route('dashboard.users.index') }}"><i data-feather="circle"></i><span
                                    class="menu-item text-truncate"
                                    data-i18n="Roles">{{ __('dashboard.users') }}</span></a>
                        </li>
                        <li><a class="@yield('createUser-active') d-flex align-items-center"
                                href="{{ route('dashboard.users.index', ['create' => 1]) }}"><i data-feather="circle"></i><span
                                    class="menu-item text-truncate"
                                    data-i18n="Permission">{{ __('dashboard.create-user') }}</span></a>
                        </li>
                    </ul>
                </li>
            @endcan





            @canany(['store_types', 'subcategories'])
                <li class="nav-item @yield('store-types-open') @yield('subcategories-open')">
                    <a class="d-flex align-items-center" href="#">
                        <i data-feather="grid"></i><span class="menu-title text-truncate">
                            {{ __('dashboard.categories_and_types') }}</span>
                    </a>
                    <ul class="menu-content">
                        @can('store_types')
                        <li>
                            <a class="@yield('store-types-active') d-flex align-items-center" href="{{ route('dashboard.store-types.setting') }}">
                                <i data-feather="circle"></i><span class="menu-item text-truncate">
                                    {{ __('dashboard.store-types') }}</span>
                            </a>
                        </li>
                        @endcan
                        @can('subcategories')
                        <li>
                            <a class="@yield('subcategories-active') d-flex align-items-center" href="{{ route('dashboard.subcategories.setting') }}">
                                <i data-feather="circle"></i><span class="menu-item text-truncate">
                                    {{ __('dashboard.subcategories') }}</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            @canany(['vendors', 'withdraw_requests'])
                <li class="nav-item @yield('vendors-open') @yield('vendor-requests-open')">
                    <a class="d-flex align-items-center" href="#">
                         <i data-feather="users"></i><span class="menu-title text-truncate">
                             {{ __('dashboard.vendors') }}</span>
                         <span class="badge badge-light-warning rounded-pill ms-auto me-1">{{ $vendorsCount }}</span>
                    </a>
                    <ul class="menu-content">
                        @can('vendors')
                        <li>
                            <a class="@yield('vendor-requests-active') d-flex align-items-center" href="{{ route('dashboard.vendors.requests') }}">
                                <i data-feather="circle"></i><span class="menu-item text-truncate">
                                    {{ __('dashboard.vendor_requests') }}</span>
                                <span class="badge badge-light-warning rounded-pill ms-auto me-1">{{ $pendingVendorRequestsCount }}</span>
                            </a>
                        </li>
                        <li>
                            <a class="@yield('vendors-active') d-flex align-items-center" href="{{ route('dashboard.vendors.setting') }}">
                                <i data-feather="circle"></i><span class="menu-item text-truncate">
                                    {{ __('dashboard.vendors') }}</span>
                                <span class="badge badge-light-success rounded-pill ms-auto me-1">{{ $approvedVendorsCount }}</span>
                            </a>
                        </li>
                        @endcan
                        @can('withdraw_requests')
                        <li>
                            <a class="@yield('withdraw-requests-active') d-flex align-items-center" href="{{ route('dashboard.withdraw-requests.index') }}">
                                <i data-feather="circle"></i><span class="menu-item text-truncate">
                                    {{ __('dashboard.withdraw-requests') }}</span>
                                <span class="badge badge-light-warning rounded-pill ms-auto me-1">{{ $pendingVendorWithdrawRequestsCount }}</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            @canany(['deliveries', 'withdraw_requests'])
                <li class="nav-item @yield('deliveries-open') @yield('delivery-requests-open')">
                    <a class="d-flex align-items-center" href="#">
                        <i data-feather="truck"></i><span class="menu-title text-truncate">
                            {{ __('dashboard.deliveries') }}</span>
                        <span class="badge badge-light-warning rounded-pill ms-auto me-1">{{ App\Models\DeliveryUser::count() }}</span>
                    </a>
                    <ul class="menu-content">
                        @can('deliveries')
                        <li>
                            <a class="@yield('delivery-requests-active') d-flex align-items-center" href="{{ route('dashboard.deliveries.requests') }}">
                                <i data-feather="circle"></i><span class="menu-item text-truncate">
                                    {{ __('dashboard.delivery_requests') }}</span>
                                <span class="badge badge-light-warning rounded-pill ms-auto me-1">{{ App\Models\DeliveryUser::where('approval_status', 'pending')->count() }}</span>
                            </a>
                        </li>
                        <li>
                            <a class="@yield('deliveries-active') d-flex align-items-center" href="{{ route('dashboard.deliveries.index') }}">
                                <i data-feather="circle"></i><span class="menu-item text-truncate">
                                    {{ __('dashboard.deliveries') }}</span>
                                <span class="badge badge-light-success rounded-pill ms-auto me-1">{{ App\Models\DeliveryUser::where('approval_status', 'approved')->count() }}</span>
                            </a>
                        </li>
                        @endcan
                        @can('withdraw_requests')
                        <li>
                            <a class="@yield('delivery-withdraw-requests-active') d-flex align-items-center" href="{{ route('dashboard.delivery-withdraw-requests.index') }}">
                                <i data-feather="circle"></i><span class="menu-item text-truncate">
                                    {{ __('dashboard.delivery_withdraw_requests') }}</span>
                                <span class="badge badge-light-warning rounded-pill ms-auto me-1">{{ $pendingDeliveryWithdrawRequestsCount }}</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            @can('settings')
                <li class="nav-item @yield('settings-open')"><a class="d-flex align-items-center" href="#">
                        <i data-feather="settings"></i><span class="menu-title text-truncate"
                            data-i18n="Roles &amp; Permission">{{ __('dashboard.settings') }}</span>
                    </a>
                    <ul class="menu-content">
                        <li><a class="@yield('settings-active') d-flex align-items-center"
                                href="{{ route('dashboard.settings') }}"><i data-feather="circle"></i><span
                                    class="menu-item text-truncate"
                                    data-i18n="Roles">{{ __('dashboard.genral-setting') }}</span></a>
                        </li>
                    </ul>
                    <ul class="menu-content">
                        <li><a class="@yield('delivery-settings-active') d-flex align-items-center"
                                href="{{ route('dashboard.delivery.setting') }}"><i data-feather="circle"></i><span
                                    class="menu-item text-truncate"
                                    data-i18n="Roles">{{ __('dashboard.delivery-settings') }}</span></a>
                        </li>
                    </ul>
                    <ul class="menu-content">
                        <li><a class="@yield('banners-active') d-flex align-items-center"
                                href="{{ route('dashboard.banners') }}"><i data-feather="circle"></i><span
                                    class="menu-item text-truncate"
                                    data-i18n="Roles">{{ __('dashboard.banners') }}</span></a>
                        </li>
                    </ul>
                    <ul class="menu-content">
                        <li><a class="@yield('about-active') d-flex align-items-center"
                                href="{{ route('dashboard.about.setting') }}"><i data-feather="circle"></i><span
                                    class="menu-item text-truncate"
                                    data-i18n="Roles">{{ __('dashboard.about-setting') }}</span></a>
                        </li>
                    </ul>
                    <ul class="menu-content">
                        <li><a class="@yield('privacy-active') d-flex align-items-center"
                                href="{{ route('dashboard.privacy.setting') }}"><i data-feather="circle"></i><span
                                    class="menu-item text-truncate"
                                    data-i18n="Roles">{{ __('dashboard.privacy-setting') }}</span></a>
                        </li>
                    </ul>
                    <ul class="menu-content">
                        <li><a class="@yield('terms-active') d-flex align-items-center"
                                href="{{ route('dashboard.terms.setting') }}"><i data-feather="circle"></i><span
                                    class="menu-item text-truncate"
                                    data-i18n="Roles">{{ __('dashboard.terms-setting') }}</span></a>
                        </li>
                    </ul>
                    <ul class="menu-content">
                        <li><a class="@yield('faqs-active') d-flex align-items-center"
                                href="{{ route('dashboard.faqs.setting') }}"><i data-feather="circle"></i><span
                                    class="menu-item text-truncate"
                                    data-i18n="Roles">{{ __('dashboard.faqs-settings') }}</span></a>
                        </li>
                    </ul>
                </li>
            @endcan

        </ul>
    </div>
</div>
