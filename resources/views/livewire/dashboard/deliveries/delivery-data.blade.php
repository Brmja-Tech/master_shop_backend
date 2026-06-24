<div>
    <!-- Statistics Cards -->
    <div class="row mb-2">
        <div class="col-lg-3 col-sm-6 col-12 mb-1">
            <div class="card h-100 mb-0 shadow-sm border-0">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="fw-bolder mb-50">{{ \App\Models\DeliveryUser::count() }}</h3>
                        <span class="text-muted">{{ __('dashboard.total_deliveries') }}</span>
                    </div>
                    <div class="avatar bg-light-primary p-50 rounded">
                        <span class="avatar-content">
                            <i class="fa-solid fa-truck fs-4"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12 mb-1">
            <div class="card h-100 mb-0 shadow-sm border-0">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="fw-bolder mb-50 text-warning">{{ \App\Models\DeliveryUser::where('approval_status', 'pending')->count() }}</h3>
                        <span class="text-muted">{{ __('dashboard.delivery_join_requests') }}</span>
                    </div>
                    <div class="avatar bg-light-warning p-50 rounded">
                        <span class="avatar-content">
                            <i class="fa-solid fa-clock fs-4"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12 mb-1">
            <div class="card h-100 mb-0 shadow-sm border-0">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="fw-bolder mb-50 text-success">{{ \App\Models\DeliveryUser::where('approval_status', 'approved')->count() }}</h3>
                        <span class="text-muted">{{ __('dashboard.approved_deliveries') }}</span>
                    </div>
                    <div class="avatar bg-light-success p-50 rounded">
                        <span class="avatar-content">
                            <i class="fa-solid fa-circle-check fs-4"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12 mb-1">
            <div class="card h-100 mb-0 shadow-sm border-0">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="fw-bolder mb-50 text-danger">{{ \App\Models\DeliveryUser::where('ban', true)->count() }}</h3>
                        <span class="text-muted">{{ __('dashboard.banned_deliveries') }}</span>
                    </div>
                    <div class="avatar bg-light-danger p-50 rounded">
                        <span class="avatar-content">
                            <i class="fa-solid fa-user-slash fs-4"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 pb-2">
        <div class="d-flex align-items-center gap-1 flex-wrap w-100">
            <!-- Search Bar -->
            <div style="flex: 1; min-width: 200px; max-width: 300px;">
                <input type="text" wire:model.live="search" class="form-control"
                    placeholder="{{ __('dashboard.search-here') }}">
            </div>
            
            <!-- Active Status Filter -->
            <div style="width: 200px;">
                <select wire:model.live="active_status" class="form-select">
                    <option value="">{{ __('dashboard.active_status') }} ({{ __('dashboard.all_statuses') }})</option>
                    <option value="1">{{ __('dashboard.active') }}</option>
                    <option value="0">{{ __('dashboard.inactive') }}</option>
                </select>
            </div>

            <!-- Ban Status Filter -->
            <div style="width: 200px;">
                <select wire:model.live="ban_status" class="form-select">
                    <option value="">{{ __('dashboard.ban_status') }} ({{ __('dashboard.all_statuses') }})</option>
                    <option value="0">{{ __('dashboard.active') }} ({{ __('dashboard.unban') ?? 'غير محظور' }})</option>
                    <option value="1">{{ __('dashboard.inactive') }} ({{ __('dashboard.ban') ?? 'محظور' }})</option>
                </select>
            </div>

            <!-- Approval Status Filter -->
            <div style="width: 200px;">
                @if ($is_request_page)
                    <select wire:model.live="approval_status" class="form-select">
                        <option value="">{{ __('dashboard.approval_status') }} ({{ __('dashboard.all_requests') }})</option>
                        <option value="pending">{{ __('dashboard.pending') }}</option>
                        <option value="rejected">{{ __('dashboard.rejected') }}</option>
                    </select>
                @else
                    <select wire:model.live="approval_status" class="form-select">
                        <option value="">{{ __('dashboard.approval_status') }} ({{ __('dashboard.all_statuses') }})</option>
                        <option value="approved">{{ __('dashboard.approved') }}</option>
                        <option value="pending">{{ __('dashboard.pending') }}</option>
                        <option value="rejected">{{ __('dashboard.rejected') }}</option>
                    </select>
                @endif
            </div>
        </div>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('dashboard.image') }}</th>
                <th>{{ __('dashboard.name') }}</th>
                <th>{{ __('dashboard.phone') }}</th>
                <th>{{ __('dashboard.email') }}</th>
                <th>{{ __('dashboard.approval_status') }}</th>
                <th>{{ __('dashboard.active_status') }}</th>
                <th>{{ __('dashboard.ban_status') }}</th>
                <th>{{ __('dashboard.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @if ($data->count() > 0)
                @foreach ($data as $item)
                    <tr wire:key="delivery-row-{{ $item->id }}">
                        <td>
                            @if ($item->img)
                                <img src="{{ asset(ltrim($item->img, '/')) }}" alt="image" width="60" height="60" class="rounded-circle object-cover">
                            @else
                                <div class="avatar bg-light-secondary rounded-circle" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                    <span class="fs-4">N/A</span>
                                </div>
                            @endif
                        </td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->phone }}</td>
                        <td>{{ $item->email ?? '--' }}</td>
                        <td>
                            @if ($item->approval_status === 'approved')
                                <span class="badge bg-light-success">{{ __('dashboard.approved') }}</span>
                            @elseif ($item->approval_status === 'rejected')
                                <span class="badge bg-light-danger">{{ __('dashboard.rejected') }}</span>
                            @else
                                <span class="badge bg-light-warning">{{ __('dashboard.pending') }}</span>
                            @endif
                        </td>
                        <td>
                            @if ($item->active_status)
                                <span class="badge bg-light-success">{{ __('dashboard.active') }}</span>
                            @else
                                <span class="badge bg-light-secondary">{{ __('dashboard.inactive') }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light-{{ $item->ban ? 'danger' : 'success' }}">
                                {{ $item->ban ? __('dashboard.inactive') : __('dashboard.active') }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <a class="btn btn-info waves-effect waves-float waves-light btn-sm"
                                    title="{{ __('dashboard.show') }}"
                                    href="{{ route('dashboard.deliveries.show', ['id' => $item->id]) }}">
                                    <i class="fa-regular fa-eye"></i>
                                </a>

                                <a class="btn btn-{{ $item->ban ? 'success' : 'danger' }} waves-effect waves-float waves-light btn-sm" href="#"
                                    wire:click.prevent="toggleBan({{ $item->id }})"
                                    title="{{ $item->ban ? __('dashboard.unban') : __('dashboard.ban') }}">
                                    @if($item->ban)
                                        <i class="fa-solid fa-unlock"></i>
                                    @else
                                        <i class="fa-solid fa-ban"></i>
                                    @endif
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="8">
                        <div class="text-danger text-center">{{ __('dashboard.no-data') }}</div>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
    <div class="mt-2 px-2">
        {{ $data->links() }}
    </div>
</div>
</div>
