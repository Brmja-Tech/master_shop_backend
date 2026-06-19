<x-update-modal title="{{ __('dashboard.update-vendor') }}">
    <div class="row mt-2">
        <div class="col-md-6 mb-1">
            <label class="form-label">{{ __('dashboard.store-name') }}</label>
            <input type="text" wire:model="store_name" class="form-control" placeholder="{{ __('dashboard.store-name') }}">
            @include('dashboard.includes.error', ['property' => 'store_name'])
        </div>
        <div class="col-md-6 mb-1">
            <label class="form-label">{{ __('dashboard.owner-name') }}</label>
            <input type="text" wire:model="owner_name" class="form-control" placeholder="{{ __('dashboard.owner-name') }}">
            @include('dashboard.includes.error', ['property' => 'owner_name'])
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-1">
            <label class="form-label">{{ __('dashboard.phone') }}</label>
            <input type="text" wire:model="phone" class="form-control" placeholder="{{ __('dashboard.phone') }}">
            @include('dashboard.includes.error', ['property' => 'phone'])
        </div>
        <div class="col-md-6 mb-1">
            <label class="form-label">{{ __('dashboard.password') }}</label>
            <input type="password" wire:model="password" class="form-control" placeholder="Leave empty to keep current password">
            @include('dashboard.includes.error', ['property' => 'password'])
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-1">
            <label class="form-label">{{ __('dashboard.store-type') }}</label>
            <select wire:model="store_type_id" class="form-control">
                <option value="">{{ __('dashboard.select-store-type') }}</option>
                @foreach ($storeTypes as $storeType)
                    <option value="{{ $storeType->id }}">{{ $storeType->name }}</option>
                @endforeach
            </select>
            @include('dashboard.includes.error', ['property' => 'store_type_id'])
        </div>
        <div class="col-md-6 mb-1">
            <label class="form-label">{{ __('dashboard.address-description') }}</label>
            <input type="text" wire:model="address_description" class="form-control" placeholder="{{ __('dashboard.address-description') }}">
            @include('dashboard.includes.error', ['property' => 'address_description'])
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-1">
            <label class="form-label">{{ __('dashboard.logo') }}</label>
            <input type="file" wire:model="logo" class="form-control">
            @if ($vendor && $vendor->logo)
                <div class="mt-1">
                    <img src="{{ url($vendor->logo) }}" alt="Current Logo" width="50" class="rounded">
                </div>
            @endif
            @include('dashboard.includes.error', ['property' => 'logo'])
        </div>
        <div class="col-md-6 mb-1">
            <label class="form-label">{{ __('dashboard.banner') }}</label>
            <input type="file" wire:model="banner" class="form-control">
            @if ($vendor && $vendor->banner)
                <div class="mt-1">
                    <img src="{{ url($vendor->banner) }}" alt="Current Banner" width="80" class="rounded">
                </div>
            @endif
            @include('dashboard.includes.error', ['property' => 'banner'])
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-1">
            <div class="form-check form-switch mt-2">
                <input type="checkbox" wire:model="is_verified" class="form-check-input" id="is_verified_update">
                <label class="form-check-label" for="is_verified_update">{{ __('dashboard.verified') }}</label>
            </div>
            @include('dashboard.includes.error', ['property' => 'is_verified'])
        </div>
    </div>
</x-update-modal>
