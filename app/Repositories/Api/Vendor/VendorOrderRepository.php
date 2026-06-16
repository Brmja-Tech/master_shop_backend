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

    public function getTodayStatsForVendor(int $vendorId): array
    {
        $today = now()->toDateString();

        $stats = Order::query()
            ->where('vendor_id', $vendorId)
            ->whereDate('created_at', $today)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN status = 'delivered' THEN total ELSE 0 END), 0) as total_sales,
                COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_count,
                COUNT(CASE WHEN status = 'delivered' THEN 1 END) as completed_count,
                COUNT(CASE WHEN status = 'accepted' THEN 1 END) as accepted_count
            ")
            ->first();

        return [
            'total_sales' => (float) ($stats->total_sales ?? 0),
            'cancelled_orders_count' => (int) ($stats->cancelled_count ?? 0),
            'completed_orders_count' => (int) ($stats->completed_count ?? 0),
            'accepted_orders_count' => (int) ($stats->accepted_count ?? 0),
        ];
    }
}
