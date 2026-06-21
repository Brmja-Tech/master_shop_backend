<?php

namespace App\Livewire\Dashboard\Settings\Vendors;

use App\Models\StoreType;
use App\Models\Vendor;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;

class VendorCreate extends Component
{
    use WithFileUploads;

    public $owner_name;
    public $phone;
    public $password;
    public $store_name;
    public $store_type_id;
    public $logo;
    public $banner;
    public $address_description;
    public $work_from;
    public $work_to;
    public $is_active = true;
    public $is_verified = true;

    protected function rules()
    {
        return [
            'owner_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'unique:vendors,phone'],
            'password' => ['required', 'string', 'min:6'],
            'store_name' => ['required', 'string', 'max:255'],
            'store_type_id' => ['required', 'integer', 'exists:store_types,id'],
            'logo' => ['nullable', 'image', 'max:5120'],
            'banner' => ['nullable', 'image', 'max:5120'],
            'address_description' => ['nullable', 'string'],
            'work_from' => ['nullable', 'string'],
            'work_to' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'is_verified' => ['boolean'],
        ];
    }

    public function submit()
    {
        $data = $this->validate();
        $data['approval_status'] = $this->is_verified ? 'approved' : 'pending';

        $imageManger = app(\App\Utils\ImageManger::class);

        if ($this->logo) {
            $data['logo'] = $imageManger->uploadImage('/uploads/vendors', $this->logo);
        }

        if ($this->banner) {
            $data['banner'] = $imageManger->uploadImage('/uploads/vendors', $this->banner);
        }

        $data['password'] = Hash::make($this->password);

        Vendor::create($data);

        $this->reset(['owner_name', 'phone', 'password', 'store_name', 'store_type_id', 'logo', 'banner', 'address_description', 'work_from', 'work_to']);
        $this->is_active = true;
        $this->is_verified = true;

        $this->dispatch('vendorAddMS');
        $this->dispatch('createModalToggle');
        $this->dispatch('refreshData')->to(VendorData::class);
    }

    public function render()
    {
        $storeTypes = StoreType::query()->orderBy('name')->get(['id', 'name']);

        return view('dashboard.settings.vendors.vendor-create', compact('storeTypes'));
    }
}
