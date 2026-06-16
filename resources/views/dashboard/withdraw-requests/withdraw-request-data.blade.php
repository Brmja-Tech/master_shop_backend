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
                <th>{{ __('dashboard.vendor') }}</th>
                <th>{{ __('dashboard.phone') }}</th>
                <th>{{ __('dashboard.withdraw_method') }}</th>
                <th>{{ __('dashboard.transfer_details') }}</th>
                <th>{{ __('dashboard.amount') }}</th>
                <th>{{ __('dashboard.orders') }}</th>
                <th>{{ __('dashboard.status') }}</th>
                <th>{{ __('dashboard.created-at') }}</th>
                <th>{{ __('dashboard.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->vendor?->store_name ?? $item->vendor?->owner_name }}</td>
                    <td>{{ $item->vendor?->phone }}</td>
                    <td>{{ __('vendor.withdraw_method_' . $item->method) }}</td>
                    <td>{{ $item->transfer_details }}</td>
                    <td>{{ number_format((float) $item->amount, 2) }}</td>
                    <td>
                        @forelse ($item->orderAllocations as $allocation)
                            <div class="mb-50 border-bottom pb-50">
                                <div>{{ __('dashboard.order_id') }}: {{ $allocation->order?->id ?? '--' }}</div>
                                <div>{{ __('dashboard.paymob_order_id') }}: {{ $allocation->order?->paymob_order_id ?? '--' }}</div>
                                <div>{{ __('dashboard.paymob_transaction_id') }}: {{ $allocation->order?->paymob_transaction_id ?? '--' }}</div>
                                <div>{{ __('dashboard.amount') }}: {{ number_format((float) $allocation->amount, 2) }}</div>
                            </div>
                        @empty
                            --
                        @endforelse
                    </td>
                    <td>
                        <span
                            class="badge bg-light-{{ $item->status?->value === 'pending' ? 'warning' : ($item->status?->value === 'approved' ? 'success' : 'danger') }}">
                            {{ __('dashboard.' . $item->status?->value) }}
                        </span>
                    </td>
                    <td>{{ $item->created_at?->format('Y-m-d h:i A') }}</td>
                    <td>
                        @if ($item->status?->value === 'pending')
                            <div class="d-flex gap-50">
                                <button type="button" class="btn btn-sm btn-success"
                                    wire:click="selectRequest({{ $item->id }})" data-bs-toggle="modal"
                                    data-bs-target="#approveWithdrawRequestModal">
                                    {{ __('dashboard.approve') }}
                                </button>
                                <button type="button" class="btn btn-sm btn-danger"
                                    wire:click="selectRequest({{ $item->id }})" data-bs-toggle="modal"
                                    data-bs-target="#rejectWithdrawRequestModal">
                                    {{ __('dashboard.reject') }}
                                </button>
                            </div>
                        @else
                            <span>{{ $item->processedByAdmin?->name ?? '--' }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center text-danger">{{ __('dashboard.no-data') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-2">
        {{ $data->links() }}
    </div>

    <div wire:ignore.self class="modal fade" id="approveWithdrawRequestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('dashboard.approve-withdraw-request') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">{{ __('dashboard.admin_note') }}</label>
                    <textarea wire:model="adminNote" class="form-control" rows="4"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                    <button type="button" class="btn btn-success"
                        wire:click="approve({{ $selectedRequestId ?: 0 }})">{{ __('dashboard.approve') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="rejectWithdrawRequestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('dashboard.reject-withdraw-request') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">{{ __('dashboard.admin_note') }}</label>
                    <textarea wire:model="adminNote" class="form-control" rows="4"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                    <button type="button" class="btn btn-danger"
                        wire:click="reject({{ $selectedRequestId ?: 0 }})">{{ __('dashboard.reject') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
