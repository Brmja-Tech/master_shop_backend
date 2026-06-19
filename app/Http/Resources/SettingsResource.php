<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name'      => $this->site_name,
            'title'     => $this->site_title,
            'desc'      => $this->site_desc,
            'address'   => $this->site_address,
            'metaKey'   => $this->meta_key,
            'metaDesc'  => $this->meta_desc,

            'phone'     => $this->site_phone,
            'whatsapp'  => $this->whatsapp,
            'email'     => $this->site_email,
            'support'   => $this->email_support,

            'facebook'  => $this->facebook,
            'xUrl'      => $this->x_url,
            'youtube'   => $this->youtube,
            'instagram' => $this->instagram,
            'tiktok'    => $this->tiktok,
            'linkedin'  => $this->linkedin,

            'logo'          => asset($this->logo),
            'favicon'       => asset($this->favicon),

            'copyright' => $this->site_copyright,
            'promotion' => $this->promotion_url,
            'delivery_price_per_km' => (float) (\App\Models\DeliverySetting::first()?->price_per_km ?? 0),
            'min_delivery_fee' => (float) (\App\Models\DeliverySetting::first()?->min_delivery_fee ?? 0),
        ];
    }
}
