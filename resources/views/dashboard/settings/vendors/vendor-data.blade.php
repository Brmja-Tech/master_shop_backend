<div class="table-responsive">
    <div class="card-header px-0">
        <div class="row w-100">
            <div class="col-md-4 mb-1">
                <input type="text" wire:model.live="search" class="form-control"
                    placeholder="{{ __('dashboard.search-here') }}">
            </div>
            @if ($is_request_page)
                <div class="col-md-4 mb-1">
                    <select wire:model.live="approval_status" class="form-select">
                        <option value="">{{ __('dashboard.all_requests') }}</option>
                        <option value="pending">{{ __('dashboard.pending') }}</option>
                        <option value="rejected">{{ __('dashboard.rejected') }}</option>
                    </select>
                </div>
            @else
                <div class="col-md-4 mb-1">
                    <select wire:model.live="store_type_id" class="form-control">
                        <option value="">{{ __('dashboard.select-store-type') }}</option>
                        @foreach ($storeTypes as $storeType)
                            <option value="{{ $storeType->id }}">{{ $storeType->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
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
                <th>{{ __('dashboard.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $item)
                @php
                    $effectiveApprovalStatus = $item->effective_approval_status ?? ($item->is_verified ? 'approved' : ($item->approval_status ?? 'pending'));
                @endphp
                <tr>
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
                            <a class="btn btn-danger waves-effect waves-float waves-light btn-sm" href="#"
                                data-id="{{ $item->id }}"
                                wire:click.prevent="$dispatch('vendorDelete', {id: {{ $item->id }}})"
                                title="{{ __('dashboard.delete') }}">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">
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
