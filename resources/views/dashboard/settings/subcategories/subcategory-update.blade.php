<x-update-modal title="{{ __('dashboard.update-subcategory') }}">
    <div class="row mt-2">
        <div class="col-md-6">
            <div class="col-sm-6">
                <label class="col-form-label">{{ __('dashboard.name') }}</label>
            </div>
            <div class="form-group">
                <input type="text" wire:model="name" placeholder="{{ __('dashboard.name') }}" class="form-control">
            </div>
            @include('dashboard.includes.error', ['property' => 'name'])
        </div>
        <div class="col-md-6">
            <div class="col-sm-6">
                <label class="col-form-label">{{ __('dashboard.store-type') }}</label>
            </div>
            <div class="form-group">
                <select wire:model="store_type_id" class="form-control">
                    <option value="">{{ __('dashboard.select-store-type') }}</option>
                    @foreach ($storeTypes as $storeType)
                        <option value="{{ $storeType->id }}">{{ $storeType->name }}</option>
                    @endforeach
                </select>
            </div>
            @include('dashboard.includes.error', ['property' => 'store_type_id'])
        </div>
    </div>
</x-update-modal>
