<?php

namespace App\Livewire\Dashboard\Settings\StoreTypes;

use App\Models\StoreType;
use Livewire\Component;
use Livewire\WithPagination;

class StoreTypeData extends Component
{
    use WithPagination;

    protected $listeners = ['refreshData' => '$refresh', 'deleteItem'];

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteItem($id)
    {
        $item = StoreType::find($id);

        if ($item) {
            $item->delete();
            $this->dispatch('itemDeleted');
        }
    }

    public function render()
    {
        $data = StoreType::query()
            ->when($this->search, fn ($query) => $query->where('name', 'like', '%' . $this->search . '%'))
            ->latest()
            ->paginate(10);

        return view('dashboard.settings.store-types.store-type-data', compact('data'));
    }
}
