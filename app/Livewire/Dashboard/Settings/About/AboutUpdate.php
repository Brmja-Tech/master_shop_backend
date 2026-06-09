<?php

namespace App\Livewire\Dashboard\Settings\About;

use App\Models\About;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class AboutUpdate extends Component
{
    use WithFileUploads;

    public $about;
    public $title_ar, $title, $desc_ar, $desc, $banner, $image;

    protected $listeners = ['refresh'];

    public function mount()
    {
        $this->about     = About::first();
        $this->title_ar  = $this->about->getTranslation('title', 'ar');
        $this->title     = $this->about->getTranslation('title', 'en');
        $this->desc_ar   = $this->about->getTranslation('desc', 'ar');
        $this->desc      = $this->about->getTranslation('desc', 'en');
        $this->banner    = $this->about->banner;
        $this->image     = $this->about->image;

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
            if ($this->about->banner && file_exists(public_path($this->about->banner)) && $this->about->banner != '/uploads/images/image.png') {
                unlink(public_path($this->about->banner));
            }
            $bannerName = uniqid() . '_' . $this->banner->getClientOriginalName();
            $this->banner->storePubliclyAs('uploads/settings', $bannerName, 'public');
            $this->about->banner = 'uploads/settings/' . $bannerName;
        }

        if ($this->image instanceof TemporaryUploadedFile && $this->image->isValid()) {
            if ($this->about->image && file_exists(public_path($this->about->image)) && $this->about->image != '/uploads/images/image.png') {
                unlink(public_path($this->about->image));
            }
            $imageName = uniqid() . '_' . $this->image->getClientOriginalName();
            $this->image->storePubliclyAs('uploads/settings', $imageName, 'public');
            $this->about->image = 'uploads/settings/' . $imageName;
        }

        $this->about->update([
            'title' => [
                'ar' => $this->title_ar,
                'en' => $this->title,
            ],
            'desc' => [
                'ar' => $this->desc_ar,
                'en' => $this->desc,
            ]
        ]);

        $this->dispatch('aboutUpdateMS');
        $this->dispatch('refresh');
    }

    public function render()
    {
        return view('dashboard.settings.about.about-update');
    }
}
