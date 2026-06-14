<x-update-modal title="{{ __('dashboard.update-store-type') }}">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                @if ($image)
                    <img src="{{ $image->temporaryUrl() }}" width="150" class="wd-80 ">
                @elseif ($oldImage)
                    <img src="{{ asset($oldImage) }}" width="150" class="wd-80 ">
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <input type="file" wire:model="image" class="form-control">
            </div>
            @include('dashboard.includes.error', ['property' => 'image'])
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="form-group">
                <input type="text" wire:model="name" placeholder="{{ __('dashboard.name') }}" class="form-control">
            </div>
            @include('dashboard.includes.error', ['property' => 'name'])
        </div>
    </div>
</x-update-modal>
