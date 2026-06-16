<?php

namespace App\Http\Resources\Api\Vendor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorWithdrawalRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vendor_id' => $this->vendor_id,
            'vendor_name' => $this->whenLoaded('vendor', fn () => $this->vendor?->store_name),
            'method' => $this->method,
            'transfer_details' => $this->transfer_details,
            'amount' => (float) $this->amount,
            'status' => $this->status?->value,
            'admin_note' => $this->admin_note,
            'processed_by_admin_id' => $this->processed_by_admin_id,
            'processed_at' => $this->processed_at?->toDateTimeString(),
            'created_at' => $this->created_at?->toDateTimeString(),
            'orders' => VendorWithdrawalRequestOrderResource::collection($this->whenLoaded('orderAllocations')),
        ];
    }
}
