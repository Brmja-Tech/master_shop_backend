<?php

namespace App\Livewire\Dashboard\Settings\Subcategories;

use App\Models\StoreType;
use App\Models\Subcategory;
use Illuminate\Validation\Rule;
use Livewire\Component;

class SubcategoryUpdate extends Component
{
    public $subcategory;
    public $name;
    public $store_type_id;

    protected $listeners = ['subcategoryUpdate'];

    public function subcategoryUpdate($id)
    {
        $this->subcategory = Subcategory::findOrFail($id);
        $this->name = $this->subcategory->name;
        $this->store_type_id = $this->subcategory->store_type_id;
        $this->dispatch('updateModalToggle');
    }

    protected function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subcategories', 'name')
                    ->ignore($this->subcategory?->id)
                    ->where(fn ($query) => $query->where('store_type_id', $this->store_type_id)),
            ],
            'store_type_id' => ['required', 'integer', 'exists:store_types,id'],
        ];
    }

    public function submit()
    {
        $data = $this->validate();

        $this->subcategory->update($data);

        $this->dispatch('subcategoryUpdateMS');
        $this->reset('name', 'store_type_id', 'subcategory');
        $this->dispatch('updateModalToggle');
        $this->dispatch('refreshData')->to(SubcategoryData::class);
    }

    public function render()
    {
        $storeTypes = StoreType::query()->orderBy('name')->get(['id', 'name']);

        return view('dashboard.settings.subcategories.subcategory-update', compact('storeTypes'));
    }
}
