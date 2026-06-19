<?php

namespace App\Livewire\Dashboard\Settings\Vendors;

use App\Models\StoreType;
use App\Models\Vendor;
use Livewire\Component;
use Livewire\WithPagination;

class VendorData extends Component
{
    use WithPagination;

    protected $listeners = ['refreshData' => '$refresh'];

    public $search = '';
    public $store_type_id = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStoreTypeId()
    {
        $this->resetPage();
    }

    public function toggleStatus($id)
    {
        $vendor = Vendor::find($id);
        if ($vendor) {
            $vendor->update([
                'is_verified' => !$vendor->is_verified
            ]);
            $this->dispatch('vendorUpdateMS');
        }
    }

    public function render()
    {
        $storeTypes = StoreType::query()->orderBy('name')->get(['id', 'name']);

        $data = Vendor::query()
            ->with(['storeType:id,name'])
            ->when($this->search, function ($query) {
                $query->where(function ($searchQuery) {
                    $searchQuery->where('store_name', 'like', '%' . $this->search . '%')
                        ->orWhere('owner_name', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->store_type_id, fn ($query) => $query->where('store_type_id', $this->store_type_id))
            ->latest()
            ->paginate(10);

        return view('dashboard.settings.vendors.vendor-data', compact('data', 'storeTypes'));
    }
}
