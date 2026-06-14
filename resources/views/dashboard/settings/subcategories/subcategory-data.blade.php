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
                <th>{{ __('dashboard.name') }}</th>
                <th>{{ __('dashboard.store-type') }}</th>
                <th>{{ __('dashboard.vendor') }}</th>
                <th>{{ __('dashboard.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->storeType?->name ?? '--' }}</td>
                    <td>{{ $item->vendor?->name ?? __('dashboard.admin-created') }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <a class="btn btn-primary waves-effect waves-float waves-light me-1" href="#"
                                title="{{ __('dashboard.update') }}"
                                wire:click.prevent="$dispatch('subcategoryUpdate', {id: {{ $item->id }}})">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>
                            <a class="btn btn-danger waves-effect waves-float waves-light" href="#"
                                wire:click.prevent="$dispatch('subcategoryDelete', {id: {{ $item->id }}})"
                                title="{{ __('dashboard.delete') }}">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
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
