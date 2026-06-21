<?php

namespace App\Http\Resources\Api\Delivery\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'img' => $this->img ? asset(ltrim($this->img, '/')) : null,
            'front_ident' => $this->front_ident ? asset(ltrim($this->front_ident, '/')) : null,
            'back_ident' => $this->back_ident ? asset(ltrim($this->back_ident, '/')) : null,
            'personal_deriving_license' => $this->personal_deriving_license ? asset(ltrim($this->personal_deriving_license, '/')) : null,
            'machine_license' => $this->machine_license ? asset(ltrim($this->machine_license, '/')) : null,
            'active_status' => $this->active_status,
            'ban' => $this->ban,
            'approval_status' => $this->approval_status,
            'lat' => $this->lat !== null ? (float) $this->lat : null,
            'lng' => $this->lng !== null ? (float) $this->lng : null,
            'balance' => (float) $this->balance,
            'max_active_orders' => $this->max_active_orders,
        ];
    }
}
