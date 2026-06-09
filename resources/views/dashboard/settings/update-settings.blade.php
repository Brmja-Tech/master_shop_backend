<div>
    <form class="form form-horizontal" wire:submit.prevent='submit'>
        {{-- ========== BASIC (Translatable) ========== --}}
        <h2 class="text-center mt-2" style="color: blue">{{ __('dashboard.basic-info') }}</h2>
        <hr style="color: blue" class="mb-2">

        <div class="row">
            <div class="mb-1 col-md-6">
                <label class="form-label">{{ __('dashboard.site-name-ar') }}</label>
                <input type="text" class="form-control" wire:model.defer="site_name_ar">
                @include('dashboard.includes.error', ['property' => 'site_name_ar'])
            </div>
            <div class="mb-1 col-md-6">
                <label class="form-label">{{ __('dashboard.site-name-en') }}</label>
                <input type="text" class="form-control" wire:model.defer="site_name_en">
                @include('dashboard.includes.error', ['property' => 'site_name_en'])
            </div>
        </div>

        <div class="row">
            <div class="mb-1 col-md-6">
                <label class="form-label">{{ __('dashboard.site-title-ar') }}</label>
                <input type="text" class="form-control" wire:model.defer="site_title_ar">
                @include('dashboard.includes.error', ['property' => 'site_title_ar'])
            </div>
            <div class="mb-1 col-md-6">
                <label class="form-label">{{ __('dashboard.site-title-en') }}</label>
                <input type="text" class="form-control" wire:model.defer="site_title_en">
                @include('dashboard.includes.error', ['property' => 'site_title_en'])
            </div>
        </div>

        <div class="mb-2">
            <label class="d-block form-label">{{ __('dashboard.site-desc-ar') }}</label>
            <textarea class="form-control" rows="3" wire:model.defer="site_desc_ar"></textarea>
            @include('dashboard.includes.error', ['property' => 'site_desc_ar'])
        </div>
        <div class="mb-2">
            <label class="d-block form-label">{{ __('dashboard.site-desc-en') }}</label>
            <textarea class="form-control" rows="3" wire:model.defer="site_desc_en"></textarea>
            @include('dashboard.includes.error', ['property' => 'site_desc_en'])
        </div>

        <div class="row">
            <div class="mb-1 col-md-6">
                <label class="form-label">{{ __('dashboard.site-address-ar') }}</label>
                <input type="text" class="form-control" wire:model.defer="site_address_ar">
                @include('dashboard.includes.error', ['property' => 'site_address_ar'])
            </div>
            <div class="mb-1 col-md-6">
                <label class="form-label">{{ __('dashboard.site-address-en') }}</label>
                <input type="text" class="form-control" wire:model.defer="site_address_en">
                @include('dashboard.includes.error', ['property' => 'site_address_en'])
            </div>
        </div>

        {{-- ========== CONTACTS ========== --}}
        <h2 class="text-center mt-3" style="color: blue">{{ __('dashboard.contacts') }}</h2>
        <hr style="color: blue" class="mb-2">

        <div class="row">
            <div class="mb-1 col-md-4">
                <label class="form-label">{{ __('dashboard.site-email') }}</label>
                <input type="email" class="form-control" wire:model.defer="site_email">
                @include('dashboard.includes.error', ['property' => 'site_email'])
            </div>
            <div class="mb-1 col-md-4">
                <label class="form-label">{{ __('dashboard.email-support') }}</label>
                <input type="email" class="form-control" wire:model.defer="email_support">
                @include('dashboard.includes.error', ['property' => 'email_support'])
            </div>
            <div class="mb-1 col-md-4">
                <label class="form-label">{{ __('dashboard.site-phone') }}</label>
                <input type="text" class="form-control" wire:model.defer="site_phone">
                @include('dashboard.includes.error', ['property' => 'site_phone'])
            </div>
        </div>

        <div class="row">
            <div class="mb-1 col-md-6">
                <label class="form-label">{{ __('dashboard.whatsapp') }}</label>
                <input type="text" class="form-control" wire:model.defer="whatsapp">
                @include('dashboard.includes.error', ['property' => 'whatsapp'])
            </div>
        </div>

        {{-- ========== SOCIAL ========== --}}
        <h2 class="text-center mt-3" style="color: blue">{{ __('dashboard.social-media') }}</h2>
        <hr style="color: blue" class="mb-2">

        <div class="row">
            <div class="mb-1 col-md-6">
                <label class="form-label">{{ __('dashboard.facebook-url') }}</label>
                <input type="text" class="form-control" wire:model.defer="facebook">
                @include('dashboard.includes.error', ['property' => 'facebook'])
            </div>
            <div class="mb-1 col-md-6">
                <label class="form-label">{{ __('dashboard.x-url') }}</label>
                <input type="text" class="form-control" wire:model.defer="x_url">
                @include('dashboard.includes.error', ['property' => 'x_url'])
            </div>
        </div>

        <div class="row">
            <div class="mb-1 col-md-4">
                <label class="form-label">{{ __('dashboard.youtube-url') }}</label>
                <input type="text" class="form-control" wire:model.defer="youtube">
                @include('dashboard.includes.error', ['property' => 'youtube'])
            </div>
            <div class="mb-1 col-md-4">
                <label class="form-label">{{ __('dashboard.instagram-url') }}</label>
                <input type="text" class="form-control" wire:model.defer="instagram">
                @include('dashboard.includes.error', ['property' => 'instagram'])
            </div>
            <div class="mb-1 col-md-4">
                <label class="form-label">{{ __('dashboard.tiktok-url') }}</label>
                <input type="text" class="form-control" wire:model.defer="tiktok">
                @include('dashboard.includes.error', ['property' => 'tiktok'])
            </div>
        </div>

        <div class="row">
            <div class="mb-1 col-md-6">
                <label class="form-label">{{ __('dashboard.linkedin-url') }}</label>
                <input type="text" class="form-control" wire:model.defer="linkedin">
                @include('dashboard.includes.error', ['property' => 'linkedin'])
            </div>
        </div>

        {{-- ========== MEDIA ========== --}}
        <h2 class="text-center mt-3" style="color: blue">{{ __('dashboard.media') }}</h2>
        <hr style="color: blue" class="mb-2">

        <div class="row mb-2">
            <div class="col-md-6">
                <label class="col-form-label">{{ __('dashboard.logo') }}</label>
                <div class="form-group mb-1">
                    @if (isset($logo) && is_object($logo))
                        <img src="{{ $logo->temporaryUrl() }}" width="150" class="wd-80 ">
                    @else
                        <img src="{{ asset($logo) }}" width="150" class="wd-80 ">
                    @endif
                </div>
                <input type="file" class="form-control" wire:model="logo">
                @include('dashboard.includes.error', ['property' => 'logo'])
            </div>

            <div class="col-md-6">
                <label class="col-form-label">{{ __('dashboard.site-favicon') }}</label>
                <div class="form-group mb-1">
                    @if (isset($favicon) && is_object($favicon))
                        <img src="{{ $favicon->temporaryUrl() }}" width="150" class="wd-80 ">
                    @else
                        <img src="{{ asset($favicon) }}" width="150" class="wd-80 ">
                    @endif
                </div>
                <input type="file" class="form-control" wire:model="favicon">
                @include('dashboard.includes.error', ['property' => 'favicon'])
            </div>
        </div>

        {{-- ========== SEO (Translatable) ========== --}}
        <h2 class="text-center mt-3" style="color: blue">{{ __('dashboard.seo') }}</h2>
        <hr style="color: blue" class="mb-2">

        <div class="mb-2">
            <label class="d-block form-label">{{ __('dashboard.meta-key-ar') }}</label>
            <textarea class="form-control" rows="2" wire:model.defer="meta_key_ar"
                placeholder="مثال: برمجة, مواقع, تطبيقات"></textarea>
            @include('dashboard.includes.error', ['property' => 'meta_key_ar'])
        </div>
        <div class="mb-2">
            <label class="d-block form-label">{{ __('dashboard.meta-key-en') }}</label>
            <textarea class="form-control" rows="2" wire:model.defer="meta_key_en" placeholder="e.g. software, web, apps"></textarea>
            @include('dashboard.includes.error', ['property' => 'meta_key_en'])
        </div>

        <div class="mb-2">
            <label class="d-block form-label">{{ __('dashboard.meta-desc-ar') }}</label>
            <textarea class="form-control" rows="3" wire:model.defer="meta_desc_ar"></textarea>
            @include('dashboard.includes.error', ['property' => 'meta_desc_ar'])
        </div>
        <div class="mb-2">
            <label class="d-block form-label">{{ __('dashboard.meta-desc-en') }}</label>
            <textarea class="form-control" rows="3" wire:model.defer="meta_desc_en"></textarea>
            @include('dashboard.includes.error', ['property' => 'meta_desc_en'])
        </div>

        {{-- ========== OTHERS ========== --}}
        <h2 class="text-center mt-3" style="color: blue">{{ __('dashboard.other') }}</h2>
        <hr style="color: blue" class="mb-2">

        <div class="row">
            <div class="mb-1 col-md-6">
                <label class="form-label">{{ __('dashboard.site-copyright') }}</label>
                <input type="text" class="form-control" wire:model.defer="site_copyright">
                @include('dashboard.includes.error', ['property' => 'site_copyright'])
            </div>
            <div class="mb-1 col-md-6">
                <label class="form-label">{{ __('dashboard.site-promo') }}</label>
                <input type="text" class="form-control" wire:model.defer="promotion_url">
                @include('dashboard.includes.error', ['property' => 'promotion_url'])
            </div>
        </div>

        <div class="row">
            <div class="mb-1 col-md-6">
                <label class="form-label">{{ __('dashboard.min-order-merchant') }}</label>
                <input type="number" step="0.01" class="form-control" wire:model.defer="min_order_merchant">
                @include('dashboard.includes.error', ['property' => 'min_order_merchant'])
            </div>
            <div class="mb-1 col-md-6">
                <label class="form-label">{{ __('dashboard.min-order-semi-merchant') }}</label>
                <input type="number" step="0.01" class="form-control" wire:model.defer="min_order_semi_merchant">
                @include('dashboard.includes.error', ['property' => 'min_order_semi_merchant'])
            </div>
        </div>

        <button type="submit" class="btn btn-primary waves-effect waves-float waves-light">
            {{ __('dashboard.submit') }}
        </button>
    </form>
</div>
