@extends('dashboard.master', ['title' => 'Settings'])
@section('settings-active', 'active')
@section('settings-open', 'open')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('dashboard.settings') }}</h4>
                </div>
                <div class="table-responsive">
                    <div class="card-body">
                        @livewire('dashboard.settings.update-settings')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    {{-- Scripts from livewire success msg --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('settingUpdateMS', function() {
                Swal.fire({
                    position: 'top-start',
                    icon: 'success',
                    title: '{{ __('dashboard.settings-update-successfully') }}',
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
@endpush
