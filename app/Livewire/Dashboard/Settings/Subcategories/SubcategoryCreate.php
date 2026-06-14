<?php

namespace App\Livewire\Dashboard\Settings\Subcategories;

use App\Models\StoreType;
use App\Models\Subcategory;
use Illuminate\Validation\Rule;
use Livewire\Component;

class SubcategoryCreate extends Component
{
    public $name;
    public $store_type_id;

    protected function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subcategories', 'name')
                    ->where(fn ($query) => $query->where('store_type_id', $this->store_type_id)),
            ],
            'store_type_id' => ['required', 'integer', 'exists:store_types,id'],
        ];
    }

    public function submit()
    {
        $data = $this->validate();

        Subcategory::create($data);

        $this->reset('name', 'store_type_id');
        $this->dispatch('subcategoryAddMS');
        $this->dispatch('createModalToggle');
        $this->dispatch('refreshData')->to(SubcategoryData::class);
    }

    public function render()
    {
        $storeTypes = StoreType::query()->orderBy('name')->get(['id', 'name']);

        return view('dashboard.settings.subcategories.subcategory-create', compact('storeTypes'));
    }
}
