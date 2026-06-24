@extends('dashboard.master', ['title' => 'Home'])
@section('dashboard-active', 'active')
@section('content')
    <section id="dashboard-ecommerce">
        @livewire('dashboard.home-statistics')
    </section>
@endsection
