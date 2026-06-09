@extends('dashboard.master', ['title' => 'About Setting'])
@section('about-active', 'active')
@section('settings-open', 'open')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('dashboard.about-setting') }}</h4>
                </div>
                <div class="table-responsive">
                    <div class="card-body">
                        @livewire('dashboard.settings.about.about-update')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
@endpush
@push('js')
    {{-- Scripts from livewire success msg --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('aboutUpdateMS', function() {
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
    </script>
    {{-- End scripts from livewire success msg --}}
@endpush
