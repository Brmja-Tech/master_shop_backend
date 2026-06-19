<div>
    <form class="form form-horizontal" wire:submit.prevent='submit'>
        <div class="row">
            <div class="mb-2 col-md-6">
                <label class="form-label">{{ __('dashboard.delivery-price-per-km') }}</label>
                <input type="number" step="0.01" class="form-control" wire:model.defer="price_per_km">
                @include('dashboard.includes.error', ['property' => 'price_per_km'])
            </div>
            <div class="mb-2 col-md-6">
                <label class="form-label">{{ __('dashboard.min-delivery-fee') }}</label>
                <input type="number" step="0.01" class="form-control" wire:model.defer="min_delivery_fee">
                @include('dashboard.includes.error', ['property' => 'min_delivery_fee'])
            </div>
        </div>

        <button type="submit" class="btn btn-primary waves-effect waves-float waves-light">
            {{ __('dashboard.submit') }}
        </button>
    </form>
</div>
