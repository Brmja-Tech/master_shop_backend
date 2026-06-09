<?php

namespace App\Livewire\Dashboard\Settings\Terms;

use App\Models\Terms;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class TermsUpdate extends Component
{
    use WithFileUploads;

    public $terms;
    public $title_ar, $title, $desc_ar, $desc, $banner, $image;

    protected $listeners = ['refresh'];

    public function mount()
    {
        $this->terms     = Terms::first();
        $this->title_ar  = $this->terms->getTranslation('title', 'ar');
        $this->title     = $this->terms->getTranslation('title', 'en');
        $this->desc_ar   = $this->terms->getTranslation('desc', 'ar');
        $this->desc      = $this->terms->getTranslation('desc', 'en');
        $this->banner    = $this->terms->banner;
        $this->image     = $this->terms->image;

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
            if ($this->terms->banner && file_exists(public_path($this->terms->banner)) && $this->terms->banner != '/uploads/images/image.png') {
                unlink(public_path($this->terms->banner));
            }
            $bannerName = uniqid() . '_' . $this->banner->getClientOriginalName();
            $this->banner->storePubliclyAs('uploads/settings', $bannerName, 'public');
            $this->terms->banner = 'uploads/settings/' . $bannerName;
        }

        if ($this->image instanceof TemporaryUploadedFile && $this->image->isValid()) {
            if ($this->terms->image && file_exists(public_path($this->terms->image)) && $this->terms->image != '/uploads/images/image.png') {
                unlink(public_path($this->terms->image));
            }
            $imageName = uniqid() . '_' . $this->image->getClientOriginalName();
            $this->image->storePubliclyAs('uploads/settings', $imageName, 'public');
            $this->terms->image = 'uploads/settings/' . $imageName;
        }

        $this->terms->update([
            'title' => [
                'ar' => $this->title_ar,
                'en' => $this->title,
            ],
            'desc' => [
                'ar' => $this->desc_ar,
                'en' => $this->desc,
            ]
        ]);

        $this->dispatch('termsUpdateMS');
        $this->dispatch('refresh');
    }
    public function render()
    {
        return view('dashboard.settings.terms.terms-update');
    }
}
