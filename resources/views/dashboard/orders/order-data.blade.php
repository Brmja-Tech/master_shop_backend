<div class="table-responsive" wire:ignore.self>
    <div class="card-header pb-1 px-0 d-flex gap-1 align-items-center">
        <input type="text" wire:model.live="search" class="form-control w-25"
            placeholder="{{ __('dashboard.search-here') }}">
        <select wire:model.live="payment_method" class="form-select w-25">
            <option value="">{{ __('dashboard.all-payment-methods') }}</option>
            <option value="cash">{{ __('dashboard.cash') }}</option>
            <option value="paymob">{{ __('dashboard.paymob') }}</option>
        </select>
    </div>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('dashboard.created-at') }}</th>
                <th>{{ __('dashboard.name') }}</th>
                <th>{{ __('dashboard.phone') }}</th>
                <th>{{ __('dashboard.total-price') }}</th>
                <th>{{ __('dashboard.payment-method') }}</th>
                <th>{{ __('dashboard.status') }}</th>
                <th>{{ __('dashboard.vendor') }}</th>
                <th>{{ __('dashboard.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $item)
                <tr>
                    <td>#{{ $item->id }}</td>
                    <td>{{ $item->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $item->customer_first_name }} {{ $item->customer_last_name }}</td>
                    <td>{{ $item->customer_phone }}</td>
                    <td class="text-primary fw-bolder">{{ $item->total }}</td>
                    <td>
                        <span class="badge bg-light-secondary">{{ $item->payment_method?->label() ?? '--' }}</span>
                    </td>
                    <td>
                        @php
                            $statusBadge = match ($item->status) {
                                \App\Enums\OrderStatus::Pending => 'warning',
                                \App\Enums\OrderStatus::Accepted => 'info',
                                \App\Enums\OrderStatus::Preparing => 'primary',
                                \App\Enums\OrderStatus::Ready => 'secondary',
                                \App\Enums\OrderStatus::OnTheWay => 'info',
                                \App\Enums\OrderStatus::Delivered => 'success',
                                \App\Enums\OrderStatus::Cancelled => 'danger',
                                default => 'light-secondary',
                            };
                        @endphp
                        <span class="badge bg-light-{{ $statusBadge }}">{{ $item->status?->label() ?? '--' }}</span>
                    </td>
                    <td>{{ $item->vendor?->store_name ?? $item->vendor?->owner_name ?? '--' }}</td>
                    <td>
                        <a class="btn btn-icon btn-sm btn-primary" href="{{ route('dashboard.orders.show', ['id' => $item->id]) }}" title="{{ __('dashboard.order-details') }}">
                            <i class="fa-regular fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-danger py-2">
                        {{ __('dashboard.no-data') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-2">
        {{ $data->links() }}
    </div>
</div>
