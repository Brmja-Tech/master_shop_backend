<div class="table-responsive" wire:ignore.self>
    <div class="card-header d-flex gap-1 flex-wrap px-0">
        <input type="text" wire:model.live="search" class="form-control w-25"
            placeholder="{{ __('dashboard.search-here') }}">

        <select wire:model.live="status" class="form-select w-auto">
            <option value="">{{ __('dashboard.all_statuses') }}</option>
            @foreach ($statuses as $item)
                <option value="{{ $item->value }}">{{ __('dashboard.' . $item->value) }}</option>
            @endforeach
        </select>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('dashboard.delivery') ?? 'Delivery Captain' }}</th>
                <th>{{ __('dashboard.phone') }}</th>
                <th>{{ __('dashboard.withdraw_method') }}</th>
                <th>{{ __('dashboard.transfer_details') }}</th>
                <th>{{ __('dashboard.amount') }}</th>
                <th>{{ __('dashboard.status') }}</th>
                <th>{{ __('dashboard.created-at') }}</th>
                <th>{{ __('dashboard.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->delivery?->name }}</td>
                    <td>{{ $item->delivery?->phone }}</td>
                    <td>{{ __('vendor.withdraw_method_' . $item->method) }}</td>
                    <td>{{ $item->transfer_details }}</td>
                    <td>{{ number_format((float) $item->amount, 2) }}</td>
                    <td>
                        <span
                            class="badge bg-light-{{ $item->status?->value === 'pending' ? 'warning' : ($item->status?->value === 'approved' ? 'success' : 'danger') }}">
                            {{ __('dashboard.' . $item->status?->value) }}
                        </span>
                    </td>
                    <td>{{ $item->created_at?->format('Y-m-d h:i A') }}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-primary"
                            wire:click="selectRequest({{ $item->id }})" data-bs-toggle="modal"
                            data-bs-target="#viewWithdrawRequestModal">
                            {{ __('dashboard.view_details') }}
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-danger">{{ __('dashboard.no-data') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-2">
        {{ $data->links() }}
    </div>

    <!-- View Details & Action Modal -->
    <div wire:ignore.self class="modal fade" id="viewWithdrawRequestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-primary fw-bold">
                        {{ __('dashboard.withdraw_request_details') }} #{{ $selectedRequestId }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                @if($selectedRequest = $this->getSelectedRequest())
                    <div class="modal-body">
                        <!-- Info Grid -->
                        <div class="row mb-2">
                            <!-- Delivery Details Card -->
                            <div class="col-md-6 mb-1 mb-md-0">
                                <h6 class="text-muted border-bottom pb-50 mb-1 fw-bold">{{ __('dashboard.delivery_details') ?? 'Delivery Captain Details' }}</h6>
                                <div class="d-flex align-items-center mb-1">
                                    <div class="avatar bg-light-primary p-50 me-1 rounded" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
                                        <span class="avatar-content fw-bold text-uppercase">{{ mb_substr($selectedRequest->delivery?->name ?? 'D', 0, 2) }}</span>
                                    </div>
                                    <div>
                                        <h5 class="mb-25 fw-bolder">{{ $selectedRequest->delivery?->name }}</h5>
                                        <span class="text-muted fs-6">{{ $selectedRequest->delivery?->email }}</span>
                                    </div>
                                </div>
                                <table class="table table-borderless table-sm">
                                    <tbody>
                                        <tr>
                                            <td class="fw-bold py-25 text-start" style="width: 35%;">{{ __('dashboard.phone') }}:</td>
                                            <td class="py-25 text-start">{{ $selectedRequest->delivery?->phone }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold py-25 text-start">{{ __('dashboard.created-at') }}:</td>
                                            <td class="py-25 text-start">{{ $selectedRequest->created_at?->format('Y-m-d h:i A') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Withdrawal & Payment Details Card -->
                            <div class="col-md-6">
                                <h6 class="text-muted border-bottom pb-50 mb-1 fw-bold">{{ __('dashboard.withdrawal_details') }}</h6>
                                <table class="table table-borderless table-sm">
                                    <tbody>
                                        <tr>
                                            <td class="fw-bold py-25 text-start" style="width: 40%;">{{ __('dashboard.withdraw_method') }}:</td>
                                            <td class="py-25 text-start">
                                                <span class="badge bg-light-info">
                                                    {{ __('vendor.withdraw_method_' . $selectedRequest->method) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold py-25 text-start">{{ __('dashboard.transfer_details') }}:</td>
                                            <td class="py-25 text-start text-wrap" style="word-break: break-all;">
                                                <code class="text-dark">{{ $selectedRequest->transfer_details }}</code>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold py-25 text-start">{{ __('dashboard.amount') }}:</td>
                                            <td class="py-25 text-start">
                                                <h4 class="text-success fw-bolder mb-0">
                                                    {{ number_format((float) $selectedRequest->amount, 2) }} <span class="fs-6 fw-normal text-muted">ج.م</span>
                                                </h4>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold py-25 text-start">{{ __('dashboard.status') }}:</td>
                                            <td class="py-25 text-start">
                                                <span class="badge bg-light-{{ $selectedRequest->status?->value === 'pending' ? 'warning' : ($selectedRequest->status?->value === 'approved' ? 'success' : 'danger') }}">
                                                    {{ __('dashboard.' . $selectedRequest->status?->value) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Associated Orders Table -->
                        <div class="card border mb-2">
                            <div class="card-header bg-light py-50">
                                <h6 class="card-title mb-0 fw-bold">{{ __('dashboard.orders') }}</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('dashboard.order_id') }}</th>
                                            <th>{{ __('dashboard.paymob_order_id') }}</th>
                                            <th>{{ __('dashboard.paymob_transaction_id') }}</th>
                                            <th class="text-end">{{ __('dashboard.delivery_fee') ?? 'Delivery Fee' }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($selectedRequest->orderAllocations as $allocation)
                                            <tr>
                                                <td class="fw-bold text-primary">#{{ $allocation->order?->id ?? '--' }}</td>
                                                <td>{{ $allocation->order?->paymob_order_id ?? '--' }}</td>
                                                <td>
                                                    @if($allocation->order?->paymob_transaction_id)
                                                        <span class="badge bg-light-secondary">{{ $allocation->order->paymob_transaction_id }}</span>
                                                    @else
                                                        --
                                                    @endif
                                                </td>
                                                <td class="text-end fw-bold text-success">{{ number_format((float) $allocation->amount, 2) }} ج.م</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-1">{{ __('dashboard.no-data') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Status Timeline / Admin Response details -->
                        @if($selectedRequest->status?->value !== 'pending')
                            <div class="alert alert-light-secondary border p-1 d-flex flex-column gap-50">
                                <div>
                                    <strong class="text-dark">{{ __('dashboard.processed_by') }}:</strong> 
                                    <span class="text-secondary">{{ $selectedRequest->processedByAdmin?->name ?? '--' }}</span>
                                    <span class="text-muted mx-50">|</span>
                                    <strong class="text-dark">{{ __('dashboard.processed_at') }}:</strong> 
                                    <span class="text-secondary">{{ $selectedRequest->processed_at ? \Carbon\Carbon::parse($selectedRequest->processed_at)->format('Y-m-d h:i A') : '--' }}</span>
                                </div>
                                @if($selectedRequest->admin_note)
                                    <div>
                                        <strong class="text-dark">{{ __('dashboard.admin_note') }}:</strong>
                                        <p class="mb-0 mt-25 text-secondary border-start ps-50" style="font-style: italic; border-left: 3px solid #ddd;">
                                            {{ $selectedRequest->admin_note }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Action Section inside the Modal for Pending requests -->
                        @if($selectedRequest->status?->value === 'pending')
                            <div class="alert alert-light-warning border p-1">
                                <h6 class="fw-bold text-warning mb-1">
                                    {{ __('dashboard.admin_action_required') }}
                                </h6>
                                <div class="mb-1">
                                    <label class="form-label fw-bold">{{ __('dashboard.admin_note') }}</label>
                                    <textarea wire:model="adminNote" class="form-control bg-white" rows="3" placeholder="{{ __('dashboard.write_admin_note_placeholder') }}"></textarea>
                                </div>
                                <div class="d-flex justify-content-end gap-1">
                                    <button type="button" class="btn btn-danger"
                                        wire:click="reject({{ $selectedRequest->id }})">
                                        {{ __('dashboard.reject') }}
                                    </button>
                                    <button type="button" class="btn btn-success"
                                        wire:click="approve({{ $selectedRequest->id }})">
                                        {{ __('dashboard.approve') }}
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
                <div class="modal-footer bg-light py-50">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('dashboard.close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
