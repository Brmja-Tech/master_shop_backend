<?php

namespace App\Livewire\Dashboard\Settings\StoreTypes;

use App\Models\StoreType;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class StoreTypeCreate extends Component
{
    use WithFileUploads;

    public $name;
    public $image;

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('store_types', 'name')],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    public function updatedName($value)
    {
        $this->name = trim((string) $value);
    }

    public function submit()
    {
        $this->name = trim((string) $this->name);
        $data = $this->validate();

        if ($this->image instanceof TemporaryUploadedFile && $this->image->isValid()) {
            $imageName = uniqid() . '_' . $this->image->getClientOriginalName();
            $this->image->storePubliclyAs('uploads/store-types', $imageName, 'public');
            $data['image'] = 'uploads/store-types/' . $imageName;
        }

        StoreType::create($data);

        $this->reset('name', 'image');
        $this->dispatch('storeTypeAddMS');
        $this->dispatch('createModalToggle');
        $this->dispatch('refreshData')->to(StoreTypeData::class);
    }

    public function render()
    {
        return view('dashboard.settings.store-types.store-type-create');
    }
}
