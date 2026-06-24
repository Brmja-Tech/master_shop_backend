<?php

namespace App\Livewire\Dashboard\Orders;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class OrderData extends Component
{
    use WithPagination;

    public $search = '';
    public $payment_method = '';

    protected $listeners = ['refreshData' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPaymentMethod()
    {
        $this->resetPage();
    }

    public function render()
    {
        $search = $this->search;
        $payment_method = $this->payment_method;

        $data = Order::with(['user', 'vendor', 'items.product.images'])
            ->when($search, function ($query) use ($search) {
                $query->where('customer_first_name', 'like', '%' . $search . '%')
                    ->orWhere('customer_last_name', 'like', '%' . $search . '%')
                    ->orWhere('customer_phone', 'like', '%' . $search . '%')
                    ->orWhere('total', 'like', '%' . $search . '%')
                    ->orWhereHas('vendor', function ($q) use ($search) {
                        $q->where('store_name', 'like', '%' . $search . '%')
                            ->orWhere('owner_name', 'like', '%' . $search . '%');
                    });
            })
            ->when($payment_method, function ($query) use ($payment_method) {
                $query->where('payment_method', $payment_method);
            })
            ->latest()
            ->paginate(10);

        return view('dashboard.orders.order-data', compact('data'));
    }
}
