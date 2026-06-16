<?php

namespace App\Repositories\Api\Vendor;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VendorOrderRepository
{
    public function getPaginatedForVendor(int $vendorId, int $perPage, ?string $status = null): LengthAwarePaginator
    {
        return Order::query()
            ->where('vendor_id', $vendorId)
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->with('user')
            ->withCount('items')
            ->latest()
            ->paginate($perPage);
    }

    public function findForVendor(int $id, int $vendorId): Order
    {
        return Order::query()
            ->where('vendor_id', $vendorId)
            ->with(['user', 'items.product.images'])
            ->findOrFail($id);
    }
}
