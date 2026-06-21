@extends('dashboard.master', ['title' => ($is_request_page ?? false) ? __('dashboard.vendor_requests') : __('dashboard.vendors')])

@if ($is_request_page ?? false)
    @section('vendor-requests-active', 'active')
    @section('vendors-open', 'open')
@else
    @section('vendors-active', 'active')
    @section('vendors-open', 'open')
@endif

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ ($is_request_page ?? false) ? __('dashboard.vendor_requests') : __('dashboard.vendors') }}</h4>
                    @if (!($is_request_page ?? false))
                        <button type="button" class="btn btn-primary waves-effect" data-bs-toggle="modal"
                            data-bs-target="#createModal">
                            <i data-feather='plus'></i> {{ __('dashboard.create-vendor') }}
                        </button>
                    @endif
                </div>
                @if (!($is_request_page ?? false))
                    @livewire('dashboard.settings.vendors.vendor-create')
                @endif
                <div class="card-body">
                    @livewire('dashboard.settings.vendors.vendor-data', ['is_request_page' => $is_request_page ?? false])
                </div>
                @if (!($is_request_page ?? false))
                    @livewire('dashboard.settings.vendors.vendor-update')
                @endif
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('vendorAddMS', function() {
                Swal.fire({
                    position: 'top-start',
                    icon: 'success',
                    title: '{{ __('dashboard.add-successfully') }}',
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
            Livewire.on('vendorUpdateMS', function() {
                Swal.fire({
                    position: 'top-start',
                    icon: 'success',
                    title: '{{ __('dashboard.update-successfully') }}',
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
            Livewire.on('vendorDelete', function(data) {
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
