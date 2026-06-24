@extends('dashboard.master', ['title' => ($is_request_page ?? false) ? __('dashboard.delivery_requests') : __('dashboard.deliveries')])

@if ($is_request_page ?? false)
    @section('delivery-requests-active', 'active')
    @section('delivery-requests-open', 'open')
@else
    @section('deliveries-active', 'active')
    @section('deliveries-open', 'open')
@endif

@section('content')
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
                    position: 'center',
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
