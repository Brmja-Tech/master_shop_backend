<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 pb-2">
        <h4 class="card-title">{{ __('dashboard.products') }}</h4>
        
        <div class="d-flex align-items-center gap-1 flex-wrap" style="flex: 1; justify-content: flex-end;">
            <!-- Search Bar -->
            <div style="min-width: 180px; max-width: 250px;">
                <input type="text" wire:model.live="search" class="form-control form-control-sm"
                    placeholder="{{ __('dashboard.search-here') }}">
            </div>
            
            <!-- Subcategory Filter -->
            <div style="width: 180px;">
                <select wire:model.live="subcategory_id" class="form-select form-select-sm">
                    <option value="">{{ __('dashboard.subcategory') }} ({{ __('dashboard.all_subcategories') }})</option>
                    @forelse ($subcategories as $subcat)
                        <option value="{{ $subcat->id }}">{{ $subcat->name }}</option>
                    @empty
                        <option value="" disabled>{{ __('dashboard.no_subcategories') }}</option>
                    @endforelse
                </select>
            </div>

            <!-- Add Product Button -->
            <button type="button" class="btn btn-primary btn-sm waves-effect waves-float waves-light"
                data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="fa-solid fa-plus"></i> {{ __('dashboard.create-product') }}
            </button>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('dashboard.image') }}</th>
                    <th>{{ __('dashboard.name') }}</th>
                    <th>{{ __('dashboard.price') }}</th>
                    <th>{{ __('dashboard.discount-precentage') }}</th>
                    <th>{{ __('dashboard.qty') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr wire:key="product-row-{{ $product->id }}">
                        <td>{{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}</td>
                        <td>
                            @php
                                $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();
                            @endphp
                            @if ($mainImage)
                                <img src="{{ url($mainImage->image) }}" alt="image" width="50" height="50" class="rounded object-cover">
                            @else
                                <div class="avatar bg-light-secondary rounded" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                    <div class="avatar-content">N/A</div>
                                </div>
                            @endif
                        </td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->price }}</td>
                        <td>{{ number_format($product->discount, 2) }}%</td>
                        <td>{{ $product->quantity }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="text-danger text-center">{{ __('dashboard.no-data') }}</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($products->hasPages())
        <div class="card-footer px-2 py-1">
            {{ $products->links() }}
        </div>
    @endif

    <!-- Add Product Modal -->
    <div class="modal fade text-start" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">{{ __('dashboard.create-product') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="saveProduct">
                    <div class="modal-body">
                        <div class="row">
                            <!-- Product Name -->
                            <div class="col-md-6 mb-1">
                                <label class="form-label" for="product_name">{{ __('dashboard.name') }} <span class="text-danger">*</span></label>
                                <input type="text" id="product_name" wire:model="name" class="form-control" placeholder="{{ __('dashboard.name') }}">
                                @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <!-- Subcategory -->
                            <div class="col-md-6 mb-1">
                                <label class="form-label" for="product_subcat">{{ __('dashboard.subcategory') }} <span class="text-danger">*</span></label>
                                <select id="product_subcat" wire:model="product_subcategory_id" class="form-select">
                                    <option value="">{{ __('dashboard.select-subcategory') }}</option>
                                    @foreach ($subcategories as $subcat)
                                        <option value="{{ $subcat->id }}">{{ $subcat->name }}</option>
                                    @endforeach
                                </select>
                                @error('product_subcategory_id') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Price -->
                            <div class="col-md-4 mb-1">
                                <label class="form-label" for="product_price">{{ __('dashboard.price') }} <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" id="product_price" wire:model="price" class="form-control" placeholder="{{ __('dashboard.price') }}">
                                @error('price') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <!-- Discount -->
                            <div class="col-md-4 mb-1">
                                <label class="form-label" for="product_discount">{{ __('dashboard.discount') }} (%)</label>
                                <input type="number" step="0.01" id="product_discount" wire:model="discount" class="form-control" placeholder="0">
                                @error('discount') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <!-- Quantity -->
                            <div class="col-md-4 mb-1">
                                <label class="form-label" for="product_qty">{{ __('dashboard.qty') }} <span class="text-danger">*</span></label>
                                <input type="number" id="product_qty" wire:model="quantity" class="form-control" placeholder="{{ __('dashboard.qty') }}">
                                @error('quantity') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Unit -->
                            <div class="col-md-6 mb-1">
                                <label class="form-label" for="product_unit">{{ __('dashboard.unit') }} <span class="text-danger">*</span></label>
                                <input type="text" id="product_unit" wire:model="unit" class="form-control" placeholder="مثال: علبة، حبة، كجم">
                                @error('unit') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <!-- Expiry Date -->
                            <div class="col-md-6 mb-1">
                                <label class="form-label" for="product_expiry">{{ __('dashboard.expiry_date') }}</label>
                                <input type="date" id="product_expiry" wire:model="expiry_date" class="form-control">
                                @error('expiry_date') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Description -->
                            <div class="col-md-12 mb-1">
                                <label class="form-label" for="product_desc">{{ __('dashboard.description') }} <span class="text-danger">*</span></label>
                                <textarea id="product_desc" wire:model="description" class="form-control" rows="3" placeholder="{{ __('dashboard.description') }}"></textarea>
                                @error('description') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Main Image -->
                            <div class="col-md-6 mb-1">
                                <label class="form-label" for="product_main_image">{{ __('dashboard.main_image') }} <span class="text-danger">*</span></label>
                                <input type="file" id="product_main_image" wire:model="main_image" class="form-control">
                                @error('main_image') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                                
                                @if ($main_image)
                                    <div class="mt-1">
                                        <img src="{{ $main_image->temporaryUrl() }}" width="100" height="100" class="rounded border object-cover shadow-sm">
                                    </div>
                                @endif
                            </div>

                            <!-- Additional Images (Gallery) -->
                            <div class="col-md-6 mb-1">
                                <label class="form-label" for="product_images">{{ __('dashboard.additional_images') }}</label>
                                <input type="file" id="product_images" wire:model="images" class="form-control" multiple>
                                @error('images') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                                @error('images.*') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                                
                                @if ($images)
                                    <div class="d-flex flex-wrap gap-50 mt-1">
                                        @foreach ($images as $img)
                                            <div class="position-relative">
                                                <img src="{{ $img->temporaryUrl() }}" width="80" height="80" class="rounded border object-cover shadow-sm">
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="saveProduct, main_image, images">
                            <span wire:loading wire:target="saveProduct, main_image, images" class="spinner-border spinner-border-sm me-50" role="status" aria-hidden="true"></span>
                            {{ __('dashboard.submit') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('productAdded', function() {
                // Close the modal
                var modalEl = document.getElementById('addProductModal');
                var modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) {
                    modal.hide();
                }
                
                // Show SweetAlert2 Success message
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: '{{ __('dashboard.product_added_successfully') }}',
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        });
    </script>
    @endpush
</div>
