@extends('dashboard.master', ['title' => ($is_request_page ?? false) ? __('dashboard.delivery_requests') : __('dashboard.deliveries')])

@if ($is_request_page ?? false)
    @section('delivery-requests-active', 'active')
    @section('delivery-requests-open', 'open')
@else
    @section('deliveries-active', 'active')
    @section('deliveries-open', 'open')
@endif

@section('content')
    <!-- Statistics Cards -->
    <div class="row mb-2">
        <div class="col-lg-3 col-sm-6 col-12 mb-1">
            <div class="card h-100 mb-0 shadow-sm border-0">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="fw-bolder mb-50">{{ \App\Models\DeliveryUser::count() }}</h3>
                        <span class="text-muted">إجمالي المناديب</span>
                    </div>
                    <div class="avatar bg-light-primary p-50 rounded">
                        <span class="avatar-content">
                            <i data-feather="truck" style="width: 24px; height: 24px;"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12 mb-1">
            <div class="card h-100 mb-0 shadow-sm border-0">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="fw-bolder mb-50 text-warning">{{ \App\Models\DeliveryUser::where('approval_status', 'pending')->count() }}</h3>
                        <span class="text-muted">طلبات الانضمام (Pending)</span>
                    </div>
                    <div class="avatar bg-light-warning p-50 rounded">
                        <span class="avatar-content">
                            <i data-feather="clock" style="width: 24px; height: 24px;"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12 mb-1">
            <div class="card h-100 mb-0 shadow-sm border-0">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="fw-bolder mb-50 text-success">{{ \App\Models\DeliveryUser::where('approval_status', 'approved')->count() }}</h3>
                        <span class="text-muted">المقبولين (Approved)</span>
                    </div>
                    <div class="avatar bg-light-success p-50 rounded">
                        <span class="avatar-content">
                            <i data-feather="check-circle" style="width: 24px; height: 24px;"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12 mb-1">
            <div class="card h-100 mb-0 shadow-sm border-0">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="fw-bolder mb-50 text-danger">{{ \App\Models\DeliveryUser::where('ban', true)->count() }}</h3>
                        <span class="text-muted">المحظورين (Banned)</span>
                    </div>
                    <div class="avatar bg-light-danger p-50 rounded">
                        <span class="avatar-content">
                            <i data-feather="user-x" style="width: 24px; height: 24px;"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ ($is_request_page ?? false) ? __('dashboard.delivery_requests') : __('dashboard.deliveries') }}</h4>
                </div>
                <div class="card-body">
                    @livewire('dashboard.deliveries.delivery-data', ['is_request_page' => $is_request_page ?? false])
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('deliveryStatusUpdate', function() {
                Swal.fire({
                    position: 'top-start',
                    icon: 'success',
                    title: '{{ __('dashboard.status-change') }}',
                    showConfirmButton: false,
                    timer: 1500,
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
            });
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('deliveryDelete', function(data) {
                Swal.fire({
                    title: "{{ __('dashboard.are_you_sure') }}",
                    text: "{{ __('dashboard.confirm_delete_message') }}",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "{{ __('dashboard.yes_delete') }}",
                    cancelButtonText: "{{ __('dashboard.cancel') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('deleteItem', {
                            id: data.id
                        });
                    }
                });
            });

            window.addEventListener('itemDeleted', function() {
                Swal.fire({
                    title: "{{ __('dashboard.success') }}",
                    text: "{{ __('dashboard.item_deleted_successfully') }}",
                    icon: "success",
                    timer: 2000
                });
            });
        });
    </script>
@endpush
