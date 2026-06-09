<div>
    <form class="form form-horizontal" wire:submit.prevent='submit'>
        <div class="row">
            <div class="mb-1 col-md-6">
                <label class="form-label">{{ __('dashboard.name-ar') }}</label>
                <input type="text" class="form-control" wire:model="title_ar"
                    placeholder="{{ __('dashboard.name-ar') }}">
                @include('dashboard.includes.error', ['property' => 'title_ar'])
            </div>
            <div class="mb-1 col-md-6">
                <label class="form-label">{{ __('dashboard.name-en') }}</label>
                <input type="text" class="form-control" wire:model="title"
                    placeholder="{{ __('dashboard.name-en') }}">
                @include('dashboard.includes.error', ['property' => 'title'])
            </div>
        </div>

        <div class="mb-1" wire:ignore>
            <label class="form-label">{{ __('dashboard.description-ar') }}</label>
            <div id="summernote_ar">{!! $desc_ar !!}</div>
            @include('dashboard.includes.error', ['property' => 'desc_ar'])
        </div>

        <div class="mb-1" wire:ignore>
            <label class="form-label">{{ __('dashboard.description-en') }}</label>
            <div id="summernote_en">{!! $desc !!}</div>
            @include('dashboard.includes.error', ['property' => 'desc'])
        </div>

        <div class="row mb-2">
            <div class="col-6">
                <label class="form-label">{{ __('dashboard.banner') }}</label>
                <div class="form-group mb-2">
                    @if (isset($banner) && is_object($banner))
                        <img src="{{ $banner->temporaryUrl() }}" width="150">
                    @else
                        <img src="{{ asset($banner) }}" width="150">
                    @endif
                </div>
                <input type="file" class="form-control" wire:model="banner">
                @include('dashboard.includes.error', ['property' => 'banner'])
            </div>

            <div class="col-6">
                <label class="form-label">{{ __('dashboard.image') }}</label>
                <div class="form-group mb-2">
                    @if (isset($image) && is_object($image))
                        <img src="{{ $image->temporaryUrl() }}" width="150">
                    @else
                        <img src="{{ asset($image) }}" width="150">
                    @endif
                </div>
                <input type="file" class="form-control" wire:model="image">
                @include('dashboard.includes.error', ['property' => 'image'])
            </div>
        </div>

        <button type="submit" class="btn btn-primary">{{ __('dashboard.submit') }}</button>
    </form>
</div>

@push('css')
    <link href="{{ asset('summernote/summernote-bs5.css') }}" rel="stylesheet">
@endpush

@push('js')
    <script src="{{ asset('summernote/summernote-bs5.js') }}"></script>
    <script src="{{ asset('summernote/lang/summernote-ar-AR.min.js') }}"></script>
    <script>
        function initSummernote(id, lang, callback) {
            $('#' + id).summernote({
                height: 300,
                lang: lang,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['insert', ['link', 'hr']],
                    ['view', ['fullscreen', 'help']],
                    ['misc', ['undo', 'redo']]
                ],
                callbacks: {
                    onChange: callback
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            initSummernote('summernote_ar', 'ar-AR', function(contents) {
                @this.set('desc_ar', contents);
            });

            initSummernote('summernote_en', 'en-US', function(contents) {
                @this.set('desc', contents);
            });

            Livewire.on('refresh', () => {
                $('#summernote_ar').summernote('code', @this.get('desc_ar'));
                $('#summernote_en').summernote('code', @this.get('desc'));
            });
        });
    </script>
@endpush
