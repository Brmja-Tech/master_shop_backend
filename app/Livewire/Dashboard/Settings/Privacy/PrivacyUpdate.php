<?php

namespace App\Livewire\Dashboard\Settings\Privacy;

use App\Models\Privacy;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PrivacyUpdate extends Component
{

    use WithFileUploads;

    public $privacy;
    public $title_ar, $title, $desc_ar, $desc, $banner, $image;

    protected $listeners = ['refresh'];

    public function mount()
    {
        $this->privacy     = Privacy::first();
        $this->title_ar  = $this->privacy->getTranslation('title', 'ar');
        $this->title     = $this->privacy->getTranslation('title', 'en');
        $this->desc_ar   = $this->privacy->getTranslation('desc', 'ar');
        $this->desc      = $this->privacy->getTranslation('desc', 'en');
        $this->banner    = $this->privacy->banner;
        $this->image     = $this->privacy->image;

        $this->resetValidation();
    }

    public function rules()
    {
        $rules = [
            'title_ar' => ['required', 'string'],
            'title' => ['required', 'string'],
            'desc_ar' => ['required', 'string'],
            'desc' => ['required', 'string'],
        ];

        if ($this->banner && $this->banner instanceof TemporaryUploadedFile) {
            $rules['banner'] = ['image', 'mimes:jpg,jpeg,png,gif,webp,avif,bmp,svg'];
        } else {
            $rules['banner'] = ['nullable'];
        }

        if ($this->image && $this->image instanceof TemporaryUploadedFile) {
            $rules['image'] = ['image', 'mimes:jpg,jpeg,png,gif,webp,avif,bmp,svg'];
        } else {
            $rules['image'] = ['nullable'];
        }

        return $rules;
    }

    public function submit()
    {
        $data = $this->validate();

        if ($this->banner instanceof TemporaryUploadedFile && $this->banner->isValid()) {
            if ($this->privacy->banner && file_exists(public_path($this->privacy->banner)) && $this->privacy->banner != '/uploads/images/image.png') {
                unlink(public_path($this->privacy->banner));
            }
            $bannerName = uniqid() . '_' . $this->banner->getClientOriginalName();
            $this->banner->storePubliclyAs('uploads/settings', $bannerName, 'public');
            $this->privacy->banner = 'uploads/settings/' . $bannerName;
        }

        if ($this->image instanceof TemporaryUploadedFile && $this->image->isValid()) {
            if ($this->privacy->image && file_exists(public_path($this->privacy->image)) && $this->privacy->image != '/uploads/images/image.png') {
                unlink(public_path($this->privacy->image));
            }
            $imageName = uniqid() . '_' . $this->image->getClientOriginalName();
            $this->image->storePubliclyAs('uploads/settings', $imageName, 'public');
            $this->privacy->image = 'uploads/settings/' . $imageName;
        }

        $this->privacy->update([
            'title' => [
                'ar' => $this->title_ar,
                'en' => $this->title,
            ],
            'desc' => [
                'ar' => $this->desc_ar,
                'en' => $this->desc,
            ]
        ]);

        $this->dispatch('privacyUpdateMS');
        $this->dispatch('refresh');
    }

    public function render()
    {
        return view('dashboard.settings.privacy.privacy-update');
    }
}
