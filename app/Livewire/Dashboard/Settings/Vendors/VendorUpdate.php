<?php

namespace App\Livewire\Dashboard\Settings\Vendors;

use App\Models\StoreType;
use App\Models\Vendor;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;

class VendorUpdate extends Component
{
    use WithFileUploads;

    public $vendor;
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
    public $is_active;
    public $is_verified;

    protected $listeners = ['vendorUpdate'];

    public function vendorUpdate($id)
    {
        $this->vendor = Vendor::findOrFail($id);
        $this->owner_name = $this->vendor->owner_name;
        $this->phone = $this->vendor->phone;
        $this->store_name = $this->vendor->store_name;
        $this->store_type_id = $this->vendor->store_type_id;
        $this->address_description = $this->vendor->address_description;
        $this->work_from = $this->vendor->work_from ? \Carbon\Carbon::parse($this->vendor->work_from)->format('H:i') : '';
        $this->work_to = $this->vendor->work_to ? \Carbon\Carbon::parse($this->vendor->work_to)->format('H:i') : '';
        $this->is_active = (bool) $this->vendor->is_active;
        $this->is_verified = (bool) $this->vendor->is_verified;
        $this->password = '';
        $this->logo = null;
        $this->banner = null;
        $this->dispatch('updateModalToggle');
    }

    protected function rules()
    {
        return [
            'owner_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', Rule::unique('vendors', 'phone')->ignore($this->vendor?->id)],
            'password' => ['nullable', 'string', 'min:6'],
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
        $data['approval_status'] = $data['is_verified']
            ? 'approved'
            : ($this->vendor->approval_status === 'rejected' ? 'rejected' : 'pending');

        $imageManger = app(\App\Utils\ImageManger::class);

        if ($this->logo) {
            if (! empty($this->vendor->logo)) {
                $imageManger->deleteImage($this->vendor->logo);
            }
            $data['logo'] = $imageManger->uploadImage('/uploads/vendors', $this->logo);
        } else {
            unset($data['logo']);
        }

        if ($this->banner) {
            if (! empty($this->vendor->banner)) {
                $imageManger->deleteImage($this->vendor->banner);
            }
            $data['banner'] = $imageManger->uploadImage('/uploads/vendors', $this->banner);
        } else {
            unset($data['banner']);
        }

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        } else {
            unset($data['password']);
        }

        $this->vendor->update($data);

        $this->dispatch('vendorUpdateMS');
        $this->reset(['owner_name', 'phone', 'password', 'store_name', 'store_type_id', 'logo', 'banner', 'address_description', 'work_from', 'work_to', 'vendor']);
        $this->dispatch('updateModalToggle');
        $this->dispatch('refreshData')->to(VendorData::class);
    }

    public function render()
    {
        $storeTypes = StoreType::query()->orderBy('name')->get(['id', 'name']);

        return view('dashboard.settings.vendors.vendor-update', compact('storeTypes'));
    }
}
