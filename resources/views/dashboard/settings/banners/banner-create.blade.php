<x-create-modal title="{{ __('dashboard.create-banner') }}">
    <div class="row">
        <div class="col-md-6">
            <div class="col-sm-6">
                <label class="col-form-label">{{ __('dashboard.live-image') }}</label>
            </div>
            <div class="form-group">
                @if ($banner)
                    <img src="{{ $banner->temporaryUrl() }}" width="150" class="wd-80 ">
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="col-sm-6">
                <label class="col-form-label">{{ __('dashboard.banner') }}
                    <code>(1300*500)</code>
                </label>
            </div>
            <div class="form-group">
                <input type="file" wire:model='banner' class="form-control">
            </div>
            @include('dashboard.includes.error', ['property' => 'banner'])
        </div>
    </div>


    <div class="row mt-4">
        <div class="col-md-6">
            <div class="col-sm-6">
                <label class="col-form-label">{{ __('dashboard.status') }}</label>
            </div>
            <div class="form-group">
                <select wire:model="status" wire:loading.attr="disabled" class="form-control" wire:target="status">
                    <option selected>{{ __('dashboard.select-status') }}</option>
                    <option value="1">{{ __('dashboard.active') }}</option>
                    <option value="0">{{ __('dashboard.inactive') }}</option>
                </select>
            </div>
            @include('dashboard.includes.error', ['property' => 'status'])
        </div>
    </div>
</x-create-modal>
