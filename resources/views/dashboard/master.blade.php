<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="{{ Config::get('app.locale') == 'ar' ? 'rtl' : 'ltr' }}">
<!-- BEGIN: Head-->
@include('dashboard.partials.head')
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern  navbar-floating footer-static  " data-open="click"
    data-menu="vertical-menu-modern" data-col="">

    <!-- BEGIN: Header-->
    @include('dashboard.partials.navbar')
    <!-- END: Header-->

    <!-- BEGIN: Main Menu-->
    @include('dashboard.partials.sidebar')
    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- Dashboard Ecommerce Starts -->
                @yield('content')
                <!-- Dashboard Ecommerce ends -->
            </div>
        </div>
    </div>
    <!-- END: Content-->

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            @flasher_error($error)
        @endforeach
    @endif

    {{-- @flasher_render --}}


    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    @include('dashboard.partials.footer')
    <!-- END: Footer-->

    <!-- BEGIN: JS-->
    @include('dashboard.partials.js')
    <!-- END: JS-->
</body>
<!-- END: Body-->

</html>
