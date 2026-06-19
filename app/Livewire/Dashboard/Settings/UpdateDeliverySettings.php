<?php

namespace App\Livewire\Dashboard\Settings;

use App\Models\DeliverySetting;
use Livewire\Component;

class UpdateDeliverySettings extends Component
{
    public DeliverySetting $settings;

    public $price_per_km;
    public $min_delivery_fee;

    public function mount(): void
    {
        $this->settings = DeliverySetting::first() ?? new DeliverySetting();
        $this->price_per_km = $this->settings->price_per_km ?? 0.00;
        $this->min_delivery_fee = $this->settings->min_delivery_fee ?? 0.00;
        $this->resetValidation();
    }

    public function rules(): array
    {
        return [
            'price_per_km' => ['required', 'numeric', 'min:0'],
            'min_delivery_fee' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function submit(): void
    {
        $data = $this->validate();

        $this->settings->fill([
            'price_per_km' => $data['price_per_km'],
            'min_delivery_fee' => $data['min_delivery_fee'],
        ]);

        $this->settings->save();

        $this->dispatch('settingUpdateMS');
    }

    public function render()
    {
        return view('dashboard.settings.update-delivery-settings');
    }
}
