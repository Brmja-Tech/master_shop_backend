<?php

namespace App\Livewire\Dashboard\Deliveries;

use App\Models\DeliveryUser;
use Livewire\Component;
use Livewire\WithPagination;

class DeliveryData extends Component
{
    use WithPagination;

    protected $listeners = ['refreshData' => '$refresh', 'deleteItem'];

    public $search = '';
    public $approval_status = '';
    public $active_status = '';
    public $ban_status = '';
    public $is_request_page = false;

    public function mount($is_request_page = false)
    {
        $this->is_request_page = $is_request_page;
        if ($this->is_request_page) {
            $this->approval_status = 'pending';
        } else {
            $this->approval_status = 'approved';
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingApprovalStatus()
    {
        $this->resetPage();
    }

    public function updatingActiveStatus()
    {
        $this->resetPage();
    }

    public function updatingBanStatus()
    {
        $this->resetPage();
    }

    public function toggleBan($id)
    {
        $delivery = DeliveryUser::find($id);
        if ($delivery) {
            $delivery->update([
                'ban' => !$delivery->ban
            ]);
            $this->dispatch('deliveryStatusUpdate');
        }
    }

    public function deleteItem($id)
    {
        $delivery = DeliveryUser::find($id);
        if ($delivery) {
            $delivery->delete();
            $this->dispatch('itemDeleted');
            $this->dispatch('refreshData');
        }
    }

    public function render()
    {
        $data = DeliveryUser::query()
            ->when($this->is_request_page, function ($query) {
                if ($this->approval_status !== '') {
                    $query->where('approval_status', $this->approval_status);
                } else {
                    $query->whereIn('approval_status', ['pending', 'rejected']);
                }
            }, function ($query) {
                if ($this->approval_status !== '') {
                    $query->where('approval_status', $this->approval_status);
                }
            })
            ->when($this->active_status !== '', function ($query) {
                $query->where('active_status', $this->active_status);
            })
            ->when($this->ban_status !== '', function ($query) {
                $query->where('ban', $this->ban_status);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.dashboard.deliveries.delivery-data', compact('data'));
    }
}
