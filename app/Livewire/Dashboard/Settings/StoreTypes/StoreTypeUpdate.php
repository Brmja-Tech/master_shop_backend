<?php

namespace App\Livewire\Dashboard\Settings\StoreTypes;

use App\Models\StoreType;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class StoreTypeUpdate extends Component
{
    use WithFileUploads;

    public $storeType;
    public $name;
    public $image;
    public $oldImage;

    protected $listeners = ['storeTypeUpdate'];

    public function storeTypeUpdate($id)
    {
        $this->storeType = StoreType::findOrFail($id);
        $this->name = $this->storeType->name;
        $this->oldImage = $this->storeType->image;
        $this->image = null;
        $this->dispatch('updateModalToggle');
    }

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('store_types', 'name')->ignore($this->storeType?->id)],
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
        } else {
            unset($data['image']);
        }

        $this->storeType->update($data);

        $this->dispatch('storeTypeUpdateMS');
        $this->reset('name', 'image', 'oldImage', 'storeType');
        $this->dispatch('updateModalToggle');
        $this->dispatch('refreshData')->to(StoreTypeData::class);
    }

    public function render()
    {
        return view('dashboard.settings.store-types.store-type-update');
    }
}
