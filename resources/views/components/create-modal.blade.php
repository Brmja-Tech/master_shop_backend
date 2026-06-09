<div class="modal fade text-start modal-success" id="createModal" tabindex="-1" aria-hidden="true" style="display: none;"
    wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel110">{{$title}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="form form-horizontal" wire:submit.prevent='submit'>
                <div class="modal-body">
                    {{$slot}}
                </div>
                <div class="modal-footer">
                    <button type="submit" type="button"
                        class="btn btn-success waves-effect waves-float waves-light">{{ __('dashboard.submit') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
