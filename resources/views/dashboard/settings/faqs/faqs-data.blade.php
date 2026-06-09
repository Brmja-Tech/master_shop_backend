<div class="table-responsive">
    <div class="card-header">
        <input type="text" wire:model.live="search" class="form-control w-25"
            placeholder="{{ __('dashboard.search-here') }}">
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('dashboard.question') }}</th>
                <th>{{ __('dashboard.answer') }}</th>
                <th>{{ __('dashboard.status') }}</th>
                <th>{{ __('dashboard.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @if ($data->count() > 0)
                @foreach ($data as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->question }}</td>
                        <td>{{ Str::words($item->answer, 5, '...') }}</td>
                        <td>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" {{ $item->status == 1 ? 'checked' : '' }}
                                    wire:click="updateStatus({{ $item->id }}, {{ $item->status == 1 ? 0 : 1 }})">
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <a class="btn btn-primary waves-effect waves-float waves-light"
                                    title="{{ __('dashboard.update') }}" href="#"
                                    wire:click.prevent="$dispatch('faqUpdate', {id: {{ $item->id }}})">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>
                                <a class="btn btn-danger waves-effect waves-float waves-light" href="#"
                                    data-id="{{ $item->id }}"
                                    wire:click.prevent="$dispatch('faqDelete', {id: {{ $item->id }}})"
                                    title="{{ __('dashboard.delete') }}">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4">
                        <div class="text-danger text-center">{{ __('dashboard.no-data') }}</div>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
    <div class=" mt-2">
        {{ $data->links() }}
    </div>
</div>
