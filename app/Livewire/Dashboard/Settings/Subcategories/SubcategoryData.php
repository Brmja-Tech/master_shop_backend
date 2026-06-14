<?php

namespace App\Livewire\Dashboard\Settings\Subcategories;

use App\Models\StoreType;
use App\Models\Subcategory;
use Livewire\Component;
use Livewire\WithPagination;

class SubcategoryData extends Component
{
    use WithPagination;

    protected $listeners = ['refreshData' => '$refresh', 'deleteItem'];

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

    public function deleteItem($id)
    {
        $item = Subcategory::find($id);

        if ($item) {
            $item->delete();
            $this->dispatch('itemDeleted');
        }
    }

    public function render()
    {
        $storeTypes = StoreType::query()->orderBy('name')->get(['id', 'name']);

        $data = Subcategory::query()
            ->with(['storeType:id,name', 'vendor:id,name'])
            ->when($this->search, function ($query) {
                $query->where(function ($searchQuery) {
                    $searchQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhereHas('storeType', function ($storeTypeQuery) {
                            $storeTypeQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('vendor', function ($vendorQuery) {
                            $vendorQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                    });
            })
            ->when($this->store_type_id, fn ($query) => $query->where('store_type_id', $this->store_type_id))
            ->latest()
            ->paginate(10);

        return view('dashboard.settings.subcategories.subcategory-data', compact('data', 'storeTypes'));
    }
}
