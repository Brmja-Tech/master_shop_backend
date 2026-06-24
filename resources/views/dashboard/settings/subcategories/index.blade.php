@extends('dashboard.master', ['title' => 'Subcategories'])
@section('subcategories-active', 'active')
@section('subcategories-open', 'open')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('dashboard.subcategories') }}</h4>
                    <button type="button" class="btn btn-primary waves-effect" data-bs-toggle="modal"
                        data-bs-target="#createModal">
                        <i data-feather='plus'></i> {{ __('dashboard.create-subcategory') }}
                    </button>
                </div>
                @livewire('dashboard.settings.subcategories.subcategory-create')
                <div class="card-body">
                    @livewire('dashboard.settings.subcategories.subcategory-data')
                </div>
                @livewire('dashboard.settings.subcategories.subcategory-update')
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('subcategoryAddMS', function() {
                Swal.fire({
                    position: 'center',
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
            Livewire.on('subcategoryUpdateMS', function() {
                Swal.fire({
                    position: 'center',
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
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('subcategoryDelete', function(data) {
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
                Livewire.dispatch('refreshData');
                Swal.fire({
                    title: "{{ __('dashboard.success') }}",
                    text: "{{ __('dashboard.item_deleted_successfully') }}",
                    icon: "success",
                    timer: 1000
                });
            });
        });
    </script>
@endpush
