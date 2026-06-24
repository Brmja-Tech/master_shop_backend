<div class="table-responsive" wire:ignore.self>
    <div class="card-header ">
        <input type="text" wire:model.live="search" class="form-control w-25"
            placeholder="{{ __('dashboard.search-here') }}">
    </div>
    <table class="table">
        <thead>
            <tr>
                {{-- <th>#</th> --}}
                <th>{{ __('dashboard.image') }}</th>
                <th>{{ __('dashboard.name') }}</th>
                <th>{{ __('dashboard.email') }}</th>
                <th>{{ __('dashboard.phone') }}</th>
                <th>{{ __('dashboard.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @if ($data->count() > 0)
                @foreach ($data as $item)
                    <tr>
                        {{-- <td>{{ $loop->iteration }}</td> --}}
                        <td>
                            <img src="{{ asset($item->image) }}" alt="image" width="80">
                        </td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->email }}</td>
                        <td>{{ $item->phone }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <a class="btn btn-info waves-effect waves-float waves-light"
                                    title="{{ __('dashboard.show') }}"
                                    href="{{ route('dashboard.user.profile', ['id' => $item->id]) }}">
                                    <i class="fa-regular fa-eye"></i>
                                </a>

                                <a class="btn btn-{{ $item->ban ? 'success' : 'danger' }} waves-effect waves-float waves-light ms-1" href="#"
                                    wire:click.prevent="$dispatch('userBanToggle', {id: {{ $item->id }}, ban: {{ $item->ban ? 'true' : 'false' }}})"
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
                <td colspan="5">
                    <div class="text-danger text-center">{{ __('dashboard.no-data') }}</div>
                </td>
            @endif
        </tbody>
    </table>
    <div class=" mt-2">
        {{ $data->links() }}
    </div>
</div>
