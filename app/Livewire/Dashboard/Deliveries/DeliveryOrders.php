<?php

namespace App\Livewire\Dashboard\Deliveries;

use App\Models\Order;
use App\Models\Vendor;
use App\Enums\PaymentMethod;
use Livewire\Component;
use Livewire\WithPagination;

class DeliveryOrders extends Component
{
    use WithPagination;

    public $deliveryId;
    public $paymentMethod = '';
    public $vendorId = '';

    protected $listeners = ['refreshData' => '$refresh'];

    public function mount($deliveryId)
    {
        $this->deliveryId = $deliveryId;
    }

    public function updatingPaymentMethod()
    {
        $this->resetPage();
    }

    public function updatingVendorId()
    {
        $this->resetPage();
    }

    public function render()
    {
        $orders = Order::with(['vendor'])
            ->where('delivery_id', $this->deliveryId)
            ->when($this->paymentMethod, function ($query) {
                $query->where('payment_method', $this->paymentMethod);
            })
            ->when($this->vendorId, function ($query) {
                $query->where('vendor_id', $this->vendorId);
            })
            ->latest()
            ->paginate(10);

        $vendors = Vendor::select('id', 'store_name', 'owner_name')->get();
        $paymentMethods = PaymentMethod::cases();

        return view('livewire.dashboard.deliveries.delivery-orders', compact('orders', 'vendors', 'paymentMethods'));
    }
}
