<?php

namespace App\Http\Resources\Api\Vendor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorWithdrawableOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'paymob_order_id' => $this->paymob_order_id,
            'paymob_transaction_id' => $this->paymob_transaction_id,
            'total' => (float) $this->total,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
