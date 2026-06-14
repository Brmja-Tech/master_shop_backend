<div class="table-responsive">
    <div class="card-header">
        <input type="text" wire:model.live="search" class="form-control w-25"
            placeholder="{{ __('dashboard.search-here') }}">
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('dashboard.image') }}</th>
                <th>{{ __('dashboard.name') }}</th>
                <th>{{ __('dashboard.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        @if ($item->image)
                            <img src="{{ asset($item->image) }}" alt="{{ $item->name }}" width="60">
                        @else
                            --
                        @endif
                    </td>
                    <td>{{ $item->name }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <a class="btn btn-primary waves-effect waves-float waves-light me-1" href="#"
                                title="{{ __('dashboard.update') }}"
                                wire:click.prevent="$dispatch('storeTypeUpdate', {id: {{ $item->id }}})">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>
                            <a class="btn btn-danger waves-effect waves-float waves-light" href="#"
                                wire:click.prevent="$dispatch('storeTypeDelete', {id: {{ $item->id }}})"
                                title="{{ __('dashboard.delete') }}">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">
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
