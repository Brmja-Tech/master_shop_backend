<?php

namespace App\Livewire\Dashboard\Settings\Banners;

use App\Models\Banner;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use App\Livewire\Dashboard\Settings\Banners\BannerData;

class BannerCreate extends Component
{
    use WithFileUploads;

    public $banner, $status = 1;

    public function rules()
    {
        return [
            'banner' => 'required|mimes:jpg,jpeg,png,gif,webp,avif,bmp,svg|max:2048',
            'status' => 'required|in:0,1',
        ];
    }

    public function submit()
    {
        $data = $this->validate();

        if ($this->banner instanceof TemporaryUploadedFile && $this->banner->isValid()) {
            $bannerName = uniqid() . '_' . $this->banner->getClientOriginalName();
            $this->banner->storePubliclyAs('uploads/settings', $bannerName, 'public');
            $data['banner'] = 'uploads/settings/' . $bannerName;
        }

        Banner::create($data);
        $this->reset('banner');
        $this->dispatch('bannerAddMS');
        $this->dispatch('createModalToggle');
        $this->dispatch('refreshData')->to(BannerData::class);
    }

    public function render()
    {
        return view('dashboard.settings.banners.banner-create');
    }
}
