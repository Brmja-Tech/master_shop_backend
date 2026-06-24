@extends('dashboard.master', ['title' => request()->has('create') ? __('dashboard.create-user') : __('dashboard.users')])

@if (request()->has('create'))
    @section('createUser-active', 'active')
    @section('createUser-open', 'open')
@else
    @section('users-active', 'active')
    @section('users-open', 'open')
@endif

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('dashboard.users') }}</h4>
                    <button type="button" class="btn btn-primary waves-effect" data-bs-toggle="modal"
                        data-bs-target="#createModal">
                        <i data-feather='plus'></i> {{ __('dashboard.create-user') }}
                    </button>
                    @livewire('dashboard.users.user-create')
                </div>
                <div class="card-body">
                    @livewire('dashboard.users.user-data')
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    {{-- Scripts from livewire success msg --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('somethingFailed', function() {
                Swal.fire({
                    position: 'top-start',
                    icon: 'error',
                    title: '{{ __('validation.something-valid') }}',
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
            Livewire.on('userAddMs', function() {
                Swal.fire({
                    position: 'top-start',
                    icon: 'success',
                    title: '{{ __('dashboard.user-add-successfully') }}',
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
            Livewire.on('userStatusUpdate', function() {
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
    </script>
    {{-- End scripts from livewire success msg --}}
    {{-- Scripts from sweetalert ban livewire --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('userBanToggle', function(data) {
                const isBanned = data.ban;
                Swal.fire({
                    title: "{{ __('dashboard.are_you_sure') }}",
                    text: isBanned ? "{{ __('dashboard.confirm_unban_message') }}" : "{{ __('dashboard.confirm_ban_message') }}",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: isBanned ? "{{ __('dashboard.yes_unban') }}" : "{{ __('dashboard.yes_ban') }}",
                    cancelButtonText: "{{ __('dashboard.cancel') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('toggleBanItem', {
                            id: data.id
                        });
                    }
                });
            });

            window.addEventListener('banToggled', function() {
                Swal.fire({
                    title: "{{ __('dashboard.success') }}",
                    text: "{{ __('dashboard.status-change') }}",
                    icon: "success",
                    timer: 2000
                });
            });
        });
        @if (request()->has('create'))
            document.addEventListener('DOMContentLoaded', function() {
                $('#createModal').modal('show');
            });
        @endif
    </script>
    {{-- End scripts from seewtalert delete livewire --}}
@endpush
