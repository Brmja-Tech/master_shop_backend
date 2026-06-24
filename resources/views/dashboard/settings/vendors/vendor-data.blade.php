<div>
    <!-- Statistics Cards -->
    <div class="row mb-2">
        <div class="col-lg-3 col-sm-6 col-12 mb-1">
            <div class="card h-100 mb-0 shadow-sm border-0">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="fw-bolder mb-50">{{ \App\Models\Vendor::count() }}</h3>
                        <span class="text-muted">{{ __('dashboard.total_vendors') }}</span>
                    </div>
                    <div class="avatar bg-light-primary p-50 rounded">
                        <span class="avatar-content">
                            <i class="fa-solid fa-shop fs-4"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12 mb-1">
            <div class="card h-100 mb-0 shadow-sm border-0">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="fw-bolder mb-50 text-warning">{{ \App\Models\Vendor::where('is_verified', false)->where(function($q) { $q->where('approval_status', 'pending')->orWhereNull('approval_status'); })->count() }}</h3>
                        <span class="text-muted">{{ __('dashboard.vendor_join_requests') }}</span>
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
                        <h3 class="fw-bolder mb-50 text-success">{{ \App\Models\Vendor::where(function($q) { $q->where('is_verified', true)->orWhere('approval_status', 'approved'); })->count() }}</h3>
                        <span class="text-muted">{{ __('dashboard.approved_vendors') }}</span>
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
                        <h3 class="fw-bolder mb-50 text-danger">{{ \App\Models\Vendor::where('ban', true)->count() }}</h3>
                        <span class="text-muted">{{ __('dashboard.banned_vendors') }}</span>
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
    <div class="card-header px-0 d-flex justify-content-between align-items-center flex-wrap gap-2 pb-2">
        <div class="d-flex align-items-center gap-1 flex-wrap w-100">
            <!-- Search Bar -->
            <div style="flex: 1; min-width: 200px; max-width: 300px;">
                <input type="text" wire:model.live="search" class="form-control"
                    placeholder="{{ __('dashboard.search-here') }}">
            </div>
            
            <!-- Store Type Filter -->
            <div style="width: 200px;">
                <select wire:model.live="store_type_id" class="form-select">
                    <option value="">{{ __('dashboard.store-type') }} ({{ __('dashboard.all_statuses') }})</option>
                    @foreach ($storeTypes as $storeType)
                        <option value="{{ $storeType->id }}">{{ $storeType->name }}</option>
                    @endforeach
                </select>
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
                <th>#</th>
                <th>{{ __('dashboard.logo') }}</th>
                <th>{{ __('dashboard.store-name') }}</th>
                <th>{{ __('dashboard.owner-name') }}</th>
                <th>{{ __('dashboard.phone') }}</th>
                <th>{{ __('dashboard.store-type') }}</th>
                <th>{{ __('dashboard.approval_status') }}</th>
                <th>{{ __('dashboard.active_status') }}</th>
                <th>{{ __('dashboard.ban_status') }}</th>
                <th>{{ __('dashboard.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $item)
                @php
                    $effectiveApprovalStatus = $item->effective_approval_status ?? ($item->is_verified ? 'approved' : ($item->approval_status ?? 'pending'));
                @endphp
                <tr wire:key="vendor-row-{{ $item->id }}">
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        @if ($item->logo)
                            <img src="{{ url($item->logo) }}" alt="Logo" class="rounded" width="40" height="40">
                        @else
                            <div class="avatar bg-light-secondary rounded">
                                <div class="avatar-content">N/A</div>
                            </div>
                        @endif
                    </td>
                    <td>{{ $item->store_name }}</td>
                    <td>{{ $item->owner_name }}</td>
                    <td>{{ $item->phone }}</td>
                    <td>{{ $item->storeType?->name ?? '--' }}</td>
                    <td>
                        <span class="badge bg-light-{{ $effectiveApprovalStatus === 'approved' ? 'success' : ($effectiveApprovalStatus === 'rejected' ? 'danger' : 'warning') }}"
                              style="cursor: pointer;"
                              wire:click="toggleStatus({{ $item->id }})"
                              title="اضغط لتغيير حالة التفعيل">
                            @if ($effectiveApprovalStatus === 'approved')
                                {{ __('dashboard.approved') }}
                            @elseif ($effectiveApprovalStatus === 'rejected')
                                {{ __('dashboard.rejected') }}
                            @else
                                {{ __('dashboard.pending') }}
                            @endif
                        </span>
                    </td>
                    <td>
                        @if ($item->is_active)
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
                                href="{{ route('dashboard.vendor.profile', ['id' => $item->id]) }}">
                                <i class="fa-regular fa-eye"></i>
                            </a>
                            @if (!$is_request_page)
                                <a class="btn btn-primary waves-effect waves-float waves-light btn-sm" href="#"
                                    title="{{ __('dashboard.update') }}"
                                    wire:click.prevent="$dispatch('vendorUpdate', {id: {{ $item->id }}})">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>
                            @endif
                            <!-- Active Status Toggle Button -->
                            <a class="btn btn-{{ $item->is_active ? 'warning' : 'success' }} waves-effect waves-float waves-light btn-sm" href="#"
                                wire:click.prevent="toggleActive({{ $item->id }})"
                                title="{{ $item->is_active ? __('dashboard.deactivate') : __('dashboard.activate') }}">
                                @if($item->is_active)
                                    <i class="fa-solid fa-toggle-on"></i>
                                @else
                                    <i class="fa-solid fa-toggle-off"></i>
                                @endif
                            </a>
                            <!-- Ban Status Toggle Button -->
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
            @empty
                <tr>
                    <td colspan="10">
                        <div class="text-danger text-center">{{ __('dashboard.no-data') }}</div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-2">
        {{ $data->links() }}
    </div>
</div>
</div>
