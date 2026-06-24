<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    {{-- <meta name="description" content="{{ $setting->meta_desc }}"> --}}
    <meta name="author" content="Master Shop">
    <title>Dashboard | {{ $title }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" href="{{ asset('dashboard') }}/app-assets/images/ico/apple-icon-120.png">
    {{-- <link rel="shortcut icon" type="image/x-icon" href="{{ asset($setting->favicon) }}"> --}}
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600"
        rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard') }}/app-assets/vendors/css/charts/apexcharts.css">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('dashboard') }}/app-assets/vendors/css/extensions/toastr.min.css">



    @if (Config::get('app.locale') == 'ar')
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboard') }}/app-assets/vendors/css/vendors-rtl.min.css">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboard') }}/app-assets/css-rtl/bootstrap.css">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboard') }}/app-assets/css-rtl/bootstrap-extended.css">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboard') }}/app-assets/css-rtl/colors.css">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboard') }}/app-assets/css-rtl/components.css">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboard') }}/app-assets/css-rtl/themes/dark-layout.css">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboard') }}/app-assets/css-rtl/themes/bordered-layout.css">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboard') }}/app-assets/css-rtl/themes/semi-dark-layout.css">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboard') }}/app-assets/css-rtl/core/menu/menu-types/vertical-menu.css">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboard') }}/app-assets/css-rtl/pages/dashboard-ecommerce.css">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboard') }}/app-assets/css-rtl/plugins/charts/chart-apex.css">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboard') }}/app-assets/css-rtl/plugins/extensions/ext-component-toastr.css">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboard') }}/app-assets/css-rtl/custom-rtl.css">
        {{-- <link rel="stylesheet" type="text/css" href="{{ asset('dashboard') }}/assets/css/style-rtl.css"> --}}
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboard') }}/app-assets/css-rtl/plugins/extensions/ext-component-sweet-alerts.css">
    @else
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboard') }}/app-assets/vendors/css/vendors.min.css">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboard') }}/app-assets/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboard') }}/app-assets/css/bootstrap-extended.css">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboard') }}/app-assets/css/colors.css">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboard') }}/app-assets/css/components.css">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboard') }}/app-assets/css/themes/dark-layout.css">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboard') }}/app-assets/css/themes/bordered-layout.css">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboard') }}/app-assets/css/themes/semi-dark-layout.css">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboard') }}/app-assets/css/core/menu/menu-types/vertical-menu.css">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboard') }}/app-assets/css/pages/dashboard-ecommerce.css">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboard') }}/app-assets/css/plugins/charts/chart-apex.css">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboard') }}/app-assets/css/plugins/extensions/ext-component-toastr.css">
        {{-- <link rel="stylesheet" type="text/css" href="{{ asset('dashboard') }}/app-assets/css/custom.css">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboard') }}/assets/css/style.css"> --}}
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboard') }}/app-assets/css/plugins/extensions/ext-component-sweet-alerts.css">
    @endif

    <link rel="stylesheet" type="text/css"
        href="{{ asset('dashboard') }}/app-assets/vendors/css/extensions/sweetalert2.min.css">

    {{-- file input to upload image and show it --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('vendor/file-input/css/fileinput.min.css') }}">
    {{-- <link href="{{ asset('vendor/file-input/themes/fa5/theme.min.css') }}" rel="stylesheet"> --}}
    {{-- end file input to upload image and show it --}}



    <style>
        :root {
            --bs-primary: #2f54eb !important;
            --bs-primary-rgb: 47, 84, 235 !important;
            --primary: #2f54eb !important;
            --bs-purple: #2f54eb !important;
            --bs-purple-rgb: 47, 84, 235 !important;
        }
        
        /* Text & Background overrides */
        .text-primary, .brand-text, .navbar-brand .brand-text {
            color: #2f54eb !important;
        }
        .bg-primary {
            background-color: #2f54eb !important;
        }
        .bg-light-primary {
            background-color: rgba(47, 84, 235, 0.12) !important;
            color: #2f54eb !important;
        }
        
        /* Buttons overrides */
        .btn-primary {
            background-color: #2f54eb !important;
            border-color: #2f54eb !important;
        }
        .btn-primary:hover, .btn-primary:active, .btn-primary:focus, .btn-primary.active {
            background-color: #1d39c4 !important;
            border-color: #1d39c4 !important;
            color: #ffffff !important;
        }
        .btn-outline-primary {
            border-color: #2f54eb !important;
            color: #2f54eb !important;
        }
        .btn-outline-primary:hover, .btn-outline-primary:active, .btn-outline-primary:focus, .btn-outline-primary.active {
            background-color: #2f54eb !important;
            color: #ffffff !important;
        }
        
        /* Sidebar active item override */
        .main-menu.menu-light .navigation > li.active > a {
            background: linear-gradient(118deg, #2f54eb, rgba(47, 84, 235, 0.7)) !important;
            box-shadow: 0 0 10px 1px rgba(47, 84, 235, 0.7) !important;
            color: #fff !important;
        }
        .main-menu.menu-light .navigation > li.sidebar-group-active > a {
            background: #f3f4f6 !important;
        }
        /* Active sub-menu item background & shadow overrides */
        .main-menu .navigation > li ul .active,
        .main-menu .navigation > li ul li.active {
            background: linear-gradient(118deg, #2f54eb, rgba(47, 84, 235, 0.7)) !important;
            box-shadow: 0 0 10px 1px rgba(47, 84, 235, 0.7) !important;
            border-radius: 4px;
            z-index: 1;
        }
        .main-menu .navigation > li ul .active > a,
        .main-menu .navigation > li ul li.active > a {
            color: #fff !important;
            background: transparent !important;
        }
        .main-menu .navigation > li ul .active > a i,
        .main-menu .navigation > li ul .active > a svg {
            color: #fff !important;
            stroke: #fff !important;
        }
        
        /* Form elements focus overrides */
        .form-check-input:checked {
            background-color: #2f54eb !important;
            border-color: #2f54eb !important;
        }
        .form-control:focus {
            border-color: #2f54eb !important;
            box-shadow: 0 3px 10px 0 rgba(47, 84, 235, 0.2) !important;
        }
        
        /* Badges overrides */
        .badge.badge-light-primary {
            background-color: rgba(47, 84, 235, 0.12) !important;
            color: #2f54eb !important;
        }
        .badge-primary {
            background-color: #2f54eb !important;
        }
        
        /* Pagination overrides */
        .page-item.active .page-link {
            background-color: #2f54eb !important;
            border-color: #2f54eb !important;
            color: #fff !important;
        }
        .page-link {
            color: #2f54eb !important;
        }
        .page-link:hover {
            color: #1d39c4 !important;
        }
        
        /* Feather icons coloring */
        .text-primary svg, .text-primary i {
            color: #2f54eb !important;
            stroke: #2f54eb !important;
        }
        
        /* Header Search and User navigation primary states */
        .nav-link-style i, .bookmark-star i {
            color: #2f54eb !important;
        }
        .dropdown-item.active, .dropdown-item:active {
            background-color: #2f54eb !important;
            color: #fff !important;
        }
        
        /* Scroll to top button */
        .scroll-top {
            background-color: #2f54eb !important;
            border-color: #2f54eb !important;
        }
        .scroll-top:hover {
            background-color: #1d39c4 !important;
        }

        /* Border overrides */
        .border-primary {
            border-color: #2f54eb !important;
        }
        
        /* Alerts */
        .alert.alert-primary {
            background-color: rgba(47, 84, 235, 0.12) !important;
            color: #2f54eb !important;
            border-color: #2f54eb !important;
        }
    </style>
    @stack('css')
</head>
