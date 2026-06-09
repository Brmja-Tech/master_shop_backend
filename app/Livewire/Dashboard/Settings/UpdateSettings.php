<?php

namespace App\Livewire\Dashboard\Settings;

use App\Models\Setting;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Http\UploadedFile;
use Livewire\TemporaryUploadedFile;
use App\Utils\ImageManger; // << استخدمنا المانجر

class UpdateSettings extends Component
{
    use WithFileUploads;

    protected $listeners = ['refresh' => '$refresh'];

    public Setting $settings;

    // Translatable fields (AR/EN)
    public $site_name_ar, $site_name_en;
    public $site_title_ar, $site_title_en;
    public $site_desc_ar, $site_desc_en;
    public $site_address_ar, $site_address_en;
    public $meta_key_ar, $meta_key_en;
    public $meta_desc_ar, $meta_desc_en;

    // Non-translatable
    public $site_phone, $whatsapp, $site_email, $email_support;
    public $facebook, $x_url, $youtube, $instagram, $tiktok, $linkedin;
    public $logo, $favicon;
    public $site_copyright, $promotion_url;

    // Minimum order amounts
    public $min_order_merchant = 0;
    public $min_order_semi_merchant = 0;

    protected $imageManager;

    public function boot(ImageManger $imageManager)
    {
        $this->imageManager = $imageManager;
    }
    public function mount(): void
    {
        $this->settings = Setting::first();


        // Translatables
        $this->site_name_ar     = $this->settings->getTranslation('site_name', 'ar');
        $this->site_name_en     = $this->settings->getTranslation('site_name', 'en');
        $this->site_title_ar    = $this->settings->getTranslation('site_title', 'ar');
        $this->site_title_en    = $this->settings->getTranslation('site_title', 'en');
        $this->site_desc_ar     = $this->settings->getTranslation('site_desc', 'ar');
        $this->site_desc_en     = $this->settings->getTranslation('site_desc', 'en');
        $this->site_address_ar  = $this->settings->getTranslation('site_address', 'ar');
        $this->site_address_en  = $this->settings->getTranslation('site_address', 'en');
        $this->meta_key_ar      = $this->settings->getTranslation('meta_key', 'ar');
        $this->meta_key_en      = $this->settings->getTranslation('meta_key', 'en');
        $this->meta_desc_ar     = $this->settings->getTranslation('meta_desc', 'ar');
        $this->meta_desc_en     = $this->settings->getTranslation('meta_desc', 'en');

        // Non-translatables
        $this->site_phone       = $this->settings->site_phone;
        $this->whatsapp         = $this->settings->whatsapp;
        $this->site_email       = $this->settings->site_email;
        $this->email_support    = $this->settings->email_support;

        $this->facebook         = $this->settings->facebook;
        $this->x_url            = $this->settings->x_url;
        $this->youtube          = $this->settings->youtube;
        $this->instagram        = $this->settings->instagram;
        $this->tiktok           = $this->settings->tiktok;
        $this->linkedin         = $this->settings->linkedin;

        $this->logo             = $this->settings->logo;
        $this->favicon          = $this->settings->favicon;

        $this->site_copyright   = $this->settings->site_copyright;
        $this->promotion_url    = $this->settings->promotion_url;
        $this->min_order_merchant = $this->settings->min_order_merchant;
        $this->min_order_semi_merchant = $this->settings->min_order_semi_merchant;

        $this->resetValidation();
    }

    public function rules(): array
    {
        $rules = [
            // Translatables
            'site_name_ar'     => ['required', 'string', 'max:255'],
            'site_name_en'     => ['required', 'string', 'max:255'],
            'site_title_ar'    => ['required', 'string', 'max:255'],
            'site_title_en'    => ['required', 'string', 'max:255'],
            'site_desc_ar'     => ['required', 'string'],
            'site_desc_en'     => ['required', 'string'],
            'site_address_ar'  => ['required', 'string', 'max:255'],
            'site_address_en'  => ['required', 'string', 'max:255'],
            'meta_key_ar'      => ['nullable', 'string'],
            'meta_key_en'      => ['nullable', 'string'],
            'meta_desc_ar'     => ['required', 'string'],
            'meta_desc_en'     => ['required', 'string'],

            // Non-translatables
            'site_phone'       => ['required', 'string', 'max:50'],
            'whatsapp'         => ['nullable', 'string', 'max:50'],
            'site_email'       => ['required', 'email', 'max:255'],
            'email_support'    => ['required', 'email', 'max:255'],

            'facebook'         => ['nullable', 'url'],
            'x_url'            => ['nullable', 'url'],
            'youtube'          => ['nullable', 'url'],
            'instagram'        => ['nullable', 'url'],
            'tiktok'           => ['nullable', 'url'],
            'linkedin'         => ['nullable', 'url'],

            'site_copyright'   => ['required', 'string'],
            'promotion_url'    => ['required', 'url'],
            'min_order_merchant' => ['required', 'numeric', 'min:0'],
            'min_order_semi_merchant' => ['required', 'numeric', 'min:0'],
        ];

        // Logo (allow SVG)
        if ($this->logo instanceof TemporaryUploadedFile) {
            $rules['logo'] = ['nullable', 'image', 'max:4096'];
        } else {
            $rules['logo'] = ['nullable'];
        }

        // Favicon (allow SVG/ICO)
        if ($this->favicon instanceof TemporaryUploadedFile) {
            $rules['favicon'] = ['nullable', 'image', 'max:2048'];
        } else {
            $rules['favicon'] = ['nullable'];
        }


        return $rules;
    }

    public function submit(): void
    {
        $data = $this->validate();

        // ===== Logo =====
        if ($this->logo instanceof UploadedFile && $this->logo->isValid()) {
            // احذف القديم لو مش الافتراضي
            if (!empty($this->settings->logo) && $this->settings->logo !== 'uploads/images/logo.png') {
                $this->imageManager->deleteImage($this->settings->logo);
            }
            // ارفع الجديد إلى disk=public -> public/storage/uploads/settings/...
            $storedRelative = $this->imageManager->uploadImage('uploads/settings', $this->logo, 'public'); // returns 'uploads/settings/xxx.ext'
            $this->settings->logo = $storedRelative; // خزّن مسار عام صالح للعرض
        }


        // ===== Favicon =====
        if ($this->favicon instanceof UploadedFile && $this->favicon->isValid()) {
            if (!empty($this->settings->favicon) && $this->settings->favicon !== 'uploads/images/logo.png') {
                $this->imageManager->deleteImage($this->settings->favicon);
            }
            $storedRelative = $this->imageManager->uploadImage('uploads/settings', $this->favicon, 'public');
            $this->settings->favicon = $storedRelative;
        }


        // ===== Translatables =====
        $this->settings->setTranslations('site_name', [
            'ar' => $data['site_name_ar'],
            'en' => $data['site_name_en'],
        ]);
        $this->settings->setTranslations('site_title', [
            'ar' => $data['site_title_ar'],
            'en' => $data['site_title_en'],
        ]);
        $this->settings->setTranslations('site_desc', [
            'ar' => $data['site_desc_ar'],
            'en' => $data['site_desc_en'],
        ]);
        $this->settings->setTranslations('site_address', [
            'ar' => $data['site_address_ar'],
            'en' => $data['site_address_en'],
        ]);
        $this->settings->setTranslations('meta_key', [
            'ar' => (string)($data['meta_key_ar'] ?? ''),
            'en' => (string)($data['meta_key_en'] ?? ''),
        ]);
        $this->settings->setTranslations('meta_desc', [
            'ar' => $data['meta_desc_ar'],
            'en' => $data['meta_desc_en'],
        ]);

        // ===== Non-translatables =====
        $this->settings->fill([
            'site_phone'      => $data['site_phone'],
            'whatsapp'        => $data['whatsapp'] ?? null,
            'site_email'      => $data['site_email'],
            'email_support'   => $data['email_support'],
            'facebook'        => $data['facebook'] ?? null,
            'x_url'           => $data['x_url'] ?? null,
            'youtube'         => $data['youtube'] ?? null,
            'instagram'       => $data['instagram'] ?? null,
            'tiktok'          => $data['tiktok'] ?? null,
            'linkedin'        => $data['linkedin'] ?? null,
            'site_copyright'  => $data['site_copyright'],
            'promotion_url'   => $data['promotion_url'],
            'min_order_merchant' => $data['min_order_merchant'],
            'min_order_semi_merchant' => $data['min_order_semi_merchant'],
        ]);

        $this->settings->save();

        $this->dispatch('settingUpdateMS');
        $this->dispatch('refresh')->to(self::class);
    }

    public function render()
    {
        return view('dashboard.settings.update-settings');
    }
}
