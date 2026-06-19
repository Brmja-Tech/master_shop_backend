<div class="table-responsive">
    <div class="card-header px-0">
        <div class="row w-100">
            <div class="col-md-4 mb-1">
                <input type="text" wire:model.live="search" class="form-control"
                    placeholder="{{ __('dashboard.search-here') }}">
            </div>
            <div class="col-md-4 mb-1">
                <select wire:model.live="store_type_id" class="form-control">
                    <option value="">{{ __('dashboard.select-store-type') }}</option>
                    @foreach ($storeTypes as $storeType)
                        <option value="{{ $storeType->id }}">{{ $storeType->name }}</option>
                    @endforeach
                </select>
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
                <th>{{ __('dashboard.verified') }}</th>
                <th>{{ __('dashboard.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $item)
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
                        <span class="badge rounded-pill {{ $item->is_verified ? 'badge-light-success' : 'badge-light-danger' }}">
                            {{ $item->is_verified ? __('dashboard.yes') : __('dashboard.no') }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <a class="btn btn-info waves-effect waves-float waves-light me-1"
                                title="{{ __('dashboard.show') }}"
                                href="{{ route('dashboard.vendor.profile', ['id' => $item->id]) }}">
                                <i class="fa-regular fa-eye"></i>
                            </a>
                            <a class="btn btn-primary waves-effect waves-float waves-light me-1" href="#"
                                title="{{ __('dashboard.update') }}"
                                wire:click.prevent="$dispatch('vendorUpdate', {id: {{ $item->id }}})">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>
                            <button class="btn btn-warning waves-effect waves-float waves-light btn-sm"
                                wire:click.prevent="toggleStatus({{ $item->id }})"
                                title="{{ $item->is_verified ? __('dashboard.inactive') : __('dashboard.active') }}">
                                <i class="fa-solid {{ $item->is_verified ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">
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
