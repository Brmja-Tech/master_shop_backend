@extends('dashboard.master', ['title' => __('dashboard.delivery_details')])
@section('deliveries-active', 'active')
@section('deliveries-open', 'open')

@section('content')

    <section class="app-user-view-account">
        <div class="row">
            <!-- Delivery Sidebar Info Card -->
            <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
                <div class="card">
                    <div class="card-body">
                        <div class="user-avatar-section">
                            <div class="d-flex align-items-center flex-column">
                                @if ($delivery->img)
                                    <img class="img-fluid rounded-circle mt-3 mb-2 shadow cursor-pointer" 
                                         src="{{ asset(ltrim($delivery->img, '/')) }}" 
                                         style="width: 110px; height: 110px; object-fit: cover;" 
                                         alt="Delivery User Image"
                                         onclick="openImageModal(this.src, '{{ $delivery->name }}')" />
                                @else
                                    <div class="avatar bg-light-secondary rounded-circle mt-3 mb-2" style="width: 110px; height: 110px; display: flex; align-items: center; justify-content: center;">
                                        <span class="fs-1">N/A</span>
                                    </div>
                                @endif
                                <div class="user-info text-center">
                                    <h4>{{ $delivery->name }}</h4>
                                    <span class="badge bg-light-secondary">{{ __('dashboard.deliveries') }}</span>
                                </div>
                            </div>
                        </div>

                        <h4 class="fw-bolder border-bottom pb-50 mb-1 mt-2">{{ __('dashboard.details-for') }} {{ $delivery->name }}</h4>
                        <div class="info-container">
                            <ul class="list-unstyled">
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">{{ __('dashboard.phone') }}:</span>
                                    <span>{{ $delivery->phone }}</span>
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">{{ __('dashboard.email') }}:</span>
                                    <span>{{ $delivery->email ?? '--' }}</span>
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">{{ __('dashboard.approval_status') }}:</span>
                                    @if ($delivery->approval_status === 'approved')
                                        <span class="badge bg-light-success">{{ __('dashboard.approved') }}</span>
                                    @elseif ($delivery->approval_status === 'rejected')
                                        <span class="badge bg-light-danger">{{ __('dashboard.rejected') }}</span>
                                    @else
                                        <span class="badge bg-light-warning">{{ __('dashboard.pending') }}</span>
                                    @endif
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">{{ __('dashboard.active_status') }}:</span>
                                    <span class="badge bg-light-{{ $delivery->active_status ? 'success' : 'secondary' }}">
                                        {{ $delivery->active_status ? __('dashboard.active') : __('dashboard.inactive') }}
                                    </span>
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">{{ __('dashboard.ban_status') }}:</span>
                                    <span class="badge bg-light-{{ $delivery->ban ? 'danger' : 'success' }}">
                                        {{ $delivery->ban ? __('dashboard.inactive') : __('dashboard.active') }}
                                    </span>
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">{{ __('dashboard.balance') }}:</span>
                                    <span>{{ $delivery->balance }}</span>
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">{{ __('dashboard.max_active_orders') }}:</span>
                                    <span>{{ $delivery->max_active_orders }}</span>
                                </li>
                            </ul>
                        </div>
                        
                        <!-- Approval & Rejection Actions -->
                        <div class="d-flex justify-content-center gap-1 mt-2 pt-1 border-top">
                            <form action="{{ route('dashboard.deliveries.status', ['id' => $delivery->id, 'status' => 'approved']) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success waves-effect waves-float waves-light">
                                    <i class="fa-solid fa-check me-25"></i> {{ __('dashboard.approve') }}
                                </button>
                            </form>
                            <form action="{{ route('dashboard.deliveries.status', ['id' => $delivery->id, 'status' => 'rejected']) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger waves-effect waves-float waves-light">
                                    <i class="fa-solid fa-xmark me-25"></i> {{ __('dashboard.reject') }}
                                </button>
                            </form>
                        </div>

                        <!-- Ban & Unban Actions -->
                        <div class="d-flex justify-content-center gap-1 mt-1">
                            <form action="{{ route('dashboard.deliveries.ban', ['id' => $delivery->id]) }}" method="POST" class="d-inline w-100">
                                @csrf
                                @if ($delivery->ban)
                                    <button type="submit" class="btn btn-outline-success w-100 waves-effect waves-float waves-light">
                                        <i class="fa-solid fa-unlock me-25"></i> إلغاء الحظر (تفعيل الحساب)
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-outline-danger w-100 waves-effect waves-float waves-light">
                                        <i class="fa-solid fa-ban me-25"></i> حظر حساب المندوب
                                    </button>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Documents Info Card -->
            <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title">وثائق ورخص المندوب (اضغط على الصورة للتكبير والفتح)</h4>
                    </div>
                    <div class="card-body mt-2">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <h5 class="fw-bolder mb-1">{{ __('dashboard.front_ident') }}</h5>
                                <div class="bg-light rounded p-1 text-center border hover-shadow" 
                                     style="cursor: pointer; transition: transform 0.2s;"
                                     onclick="openImageModal('{{ asset(ltrim($delivery->front_ident, '/')) }}', '{{ __('dashboard.front_ident') }}')">
                                    <img src="{{ asset(ltrim($delivery->front_ident, '/')) }}" 
                                         class="img-fluid rounded shadow-sm" 
                                         style="max-height: 200px; object-fit: contain;" 
                                         alt="front ident">
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-2">
                                <h5 class="fw-bolder mb-1">{{ __('dashboard.back_ident') }}</h5>
                                <div class="bg-light rounded p-1 text-center border hover-shadow" 
                                     style="cursor: pointer; transition: transform 0.2s;"
                                     onclick="openImageModal('{{ asset(ltrim($delivery->back_ident, '/')) }}', '{{ __('dashboard.back_ident') }}')">
                                    <img src="{{ asset(ltrim($delivery->back_ident, '/')) }}" 
                                         class="img-fluid rounded shadow-sm" 
                                         style="max-height: 200px; object-fit: contain;" 
                                         alt="back ident">
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-2">
                                <h5 class="fw-bolder mb-1">{{ __('dashboard.personal_deriving_license') }}</h5>
                                <div class="bg-light rounded p-1 text-center border hover-shadow" 
                                     style="cursor: pointer; transition: transform 0.2s;"
                                     onclick="openImageModal('{{ asset(ltrim($delivery->personal_deriving_license, '/')) }}', '{{ __('dashboard.personal_deriving_license') }}')">
                                    <img src="{{ asset(ltrim($delivery->personal_deriving_license, '/')) }}" 
                                         class="img-fluid rounded shadow-sm" 
                                         style="max-height: 200px; object-fit: contain;" 
                                         alt="personal driving license">
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-2">
                                <h5 class="fw-bolder mb-1">{{ __('dashboard.machine_license') }}</h5>
                                <div class="bg-light rounded p-1 text-center border hover-shadow" 
                                     style="cursor: pointer; transition: transform 0.2s;"
                                     onclick="openImageModal('{{ asset(ltrim($delivery->machine_license, '/')) }}', '{{ __('dashboard.machine_license') }}')">
                                    <img src="{{ asset(ltrim($delivery->machine_license, '/')) }}" 
                                         class="img-fluid rounded shadow-sm" 
                                         style="max-height: 200px; object-fit: contain;" 
                                         alt="machine license">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders Table -->
                <div class="mt-2">
                    @livewire('dashboard.deliveries.delivery-orders', ['deliveryId' => $delivery->id])
                </div>
            </div>
        </div>
    </section>

    <!-- Custom Image Preview Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title" id="imagePreviewTitle">معاينة الصورة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-2 bg-dark">
                    <img id="previewImg" src="" class="img-fluid rounded" style="max-height: 75vh; object-fit: contain;" alt="Preview">
                </div>
                <div class="modal-footer justify-content-between">
                    <a id="downloadImgLink" href="" target="_blank" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-up-right-from-square me-25"></i> فتح في علامة تبويب جديدة
                    </a>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script>
        function openImageModal(imgSrc, title) {
            document.getElementById('previewImg').src = imgSrc;
            document.getElementById('imagePreviewTitle').innerText = title;
            document.getElementById('downloadImgLink').href = imgSrc;
            
            var modalEl = document.getElementById('imagePreviewModal');
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    </script>
    <style>
        .hover-shadow:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
        }
        .cursor-pointer {
            cursor: pointer;
        }
    </style>
@endpush
