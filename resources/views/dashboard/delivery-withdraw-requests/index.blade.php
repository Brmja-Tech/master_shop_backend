@extends('dashboard.master', ['title' => 'Delivery Withdraw Requests'])
@section('delivery-withdraw-requests-active', 'active')
@section('delivery-withdraw-requests-open', 'open')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('dashboard.delivery_withdraw_requests') ?? 'Delivery Withdraw Requests' }}</h4>
                </div>
                <div class="card-body">
                    @livewire('dashboard.delivery-withdraw-requests.delivery-withdraw-request-data')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('deliveryWithdrawRequestApproved', function() {
                const viewModal = bootstrap.Modal.getInstance(document.getElementById('viewWithdrawRequestModal'));
                if (viewModal) viewModal.hide();
 
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: '{{ __('dashboard.withdraw-request-approved-successfully') }}',
                    showConfirmButton: false,
                    timer: 1500,
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
            });
 
            Livewire.on('deliveryWithdrawRequestRejected', function() {
                const viewModal = bootstrap.Modal.getInstance(document.getElementById('viewWithdrawRequestModal'));
                if (viewModal) viewModal.hide();
 
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: '{{ __('dashboard.withdraw-request-rejected-successfully') }}',
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
@endpush
