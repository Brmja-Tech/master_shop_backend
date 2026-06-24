<?php

namespace App\Livewire\Dashboard\Settings\Vendors;

use App\Models\Vendor;
use App\Models\Product;
use App\Models\Subcategory;
use App\Utils\ImageManger;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class VendorProducts extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['refreshData' => '$refresh'];

    public $vendorId;
    public $search = '';
    public $subcategory_id = '';

    // Form fields for adding product
    public $name;
    public $description;
    public $product_subcategory_id;
    public $price;
    public $discount = 0;
    public $quantity;
    public $unit;
    public $expiry_date;
    public $main_image;
    public $images = [];

    public function mount($vendorId)
    {
        $this->vendorId = $vendorId;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSubcategoryId()
    {
        $this->resetPage();
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'product_subcategory_id' => 'required|integer|exists:subcategories,id',
            'quantity' => 'required|integer|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'unit' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'expiry_date' => 'nullable|date|after:today',
            'main_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'name' => __('dashboard.name'),
            'description' => __('dashboard.description'),
            'product_subcategory_id' => __('dashboard.subcategory'),
            'quantity' => __('dashboard.qty'),
            'discount' => __('dashboard.discount'),
            'unit' => __('dashboard.unit'),
            'price' => __('dashboard.price'),
            'expiry_date' => __('dashboard.expiry_date'),
            'main_image' => __('dashboard.main_image'),
            'images' => __('dashboard.additional_images'),
        ];
    }

    public function saveProduct()
    {
        $this->validate();

        $vendor = Vendor::findOrFail($this->vendorId);

        // Upload image using ImageManger
        $imageManger = new ImageManger();
        $storedMainImage = $imageManger->uploadImage('/uploads/products', $this->main_image);

        // Create product
        $product = Product::create([
            'vendor_id' => $vendor->id,
            'subcategory_id' => $this->product_subcategory_id,
            'name' => $this->name,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'remaining_quantity' => $this->quantity,
            'discount' => $this->discount ?: 0,
            'is_available' => true,
            'unit' => $this->unit,
            'price' => $this->price,
            'expiry_date' => $this->expiry_date,
        ]);

        // Create main image record
        $product->images()->create([
            'image' => $storedMainImage,
            'is_main' => true,
        ]);

        // Create gallery images records
        if (!empty($this->images)) {
            $galleryImages = $imageManger->uploadMultiImage('/uploads/products', $this->images);
            foreach ($galleryImages as $storedGalleryImage) {
                $product->images()->create([
                    'image' => $storedGalleryImage,
                    'is_main' => false,
                ]);
            }
        }

        $this->reset([
            'name',
            'description',
            'product_subcategory_id',
            'quantity',
            'discount',
            'unit',
            'price',
            'expiry_date',
            'main_image',
            'images',
        ]);

        $this->resetErrorBag();

        $this->dispatch('productAdded');
        $this->dispatch('refreshData');
    }

    public function render()
    {
        $vendor = Vendor::findOrFail($this->vendorId);

        // Load subcategories for this store type
        $subcategories = Subcategory::where('store_type_id', $vendor->store_type_id)
            ->where(function ($q) use ($vendor) {
                $q->whereNull('vendor_id')
                  ->orWhere('vendor_id', $vendor->id);
            })
            ->get(['id', 'name']);

        // Load products paginated and filtered
        $products = Product::where('vendor_id', $this->vendorId)
            ->with(['images'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->subcategory_id, function ($query) {
                $query->where('subcategory_id', $this->subcategory_id);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.dashboard.settings.vendors.vendor-products', compact('products', 'subcategories'));
    }
}
