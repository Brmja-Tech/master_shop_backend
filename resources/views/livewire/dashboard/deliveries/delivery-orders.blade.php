<div class="card">
    <div class="card-header d-flex flex-md-row flex-column justify-content-between align-items-md-center align-items-start gap-2 border-bottom pb-1">
        <h4 class="card-title mb-0">{{ __('dashboard.user-orders') }} ({{ __('dashboard.deliveries') }})</h4>
        
        <div class="d-flex flex-wrap gap-2">
            <!-- Payment Method Filter -->
            <div>
                <select wire:model.live="paymentMethod" class="form-select form-select-sm">
                    <option value="">{{ __('dashboard.all-payment-methods') }}</option>
                    @foreach ($paymentMethods as $method)
                        <option value="{{ $method->value }}">{{ $method->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Vendor Filter -->
            <div>
                <select wire:model.live="vendorId" class="form-select form-select-sm">
                    <option value="">{{ __('dashboard.all-vendors') }}</option>
                    @foreach ($vendors as $vendor)
                        <option value="{{ $vendor->id }}">{{ $vendor->store_name ?? $vendor->owner_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('dashboard.created-at') }}</th>
                    <th>{{ __('dashboard.phone') }}</th>
                    <th>{{ __('dashboard.total-price') }}</th>
                    <th>{{ __('dashboard.payment-method') }}</th>
                    <th>{{ __('dashboard.vendor') }}</th>
                    <th>{{ __('dashboard.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $item)
                    <tr wire:key="order-row-{{ $item->id }}">
                        <td>#{{ $item->id }}</td>
                        <td>{{ $item->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $item->customer_phone }}</td>
                        <td class="text-primary fw-bolder">{{ $item->total }}</td>
                        <td>
                            <span class="badge bg-light-secondary">{{ $item->payment_method?->label() ?? '--' }}</span>
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
                        <td colspan="7" class="text-center text-danger py-2">
                            {{ __('dashboard.no-data') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($orders->hasPages())
        <div class="card-footer pb-0">
            {{ $orders->links() }}
        </div>
    @endif
</div>
