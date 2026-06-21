<?php

namespace App\Livewire\Dashboard\Settings\Vendors;

use App\Models\StoreType;
use App\Models\Vendor;
use Livewire\Component;
use Livewire\WithPagination;

class VendorData extends Component
{
    use WithPagination;

    protected $listeners = ['refreshData' => '$refresh', 'deleteItem'];

    public $search = '';
    public $store_type_id = '';
    public $approval_status = '';
    public $is_request_page = false;

    protected function resolveEffectiveApprovalStatus(Vendor $vendor): string
    {
        if ((bool) $vendor->is_verified) {
            return 'approved';
        }

        return $vendor->approval_status ?: 'pending';
    }

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

    public function updatingStoreTypeId()
    {
        $this->resetPage();
    }

    public function updatingApprovalStatus()
    {
        $this->resetPage();
    }

    public function toggleBan($id)
    {
        $vendor = Vendor::find($id);
        if ($vendor) {
            $vendor->update([
                'ban' => !$vendor->ban
            ]);
            $this->dispatch('vendorUpdateMS');
        }
    }

    public function toggleStatus($id)
    {
        $vendor = Vendor::find($id);
        if ($vendor) {
            $newStatus = !$vendor->is_verified;
            $vendor->update([
                'is_verified' => $newStatus,
                'approval_status' => $newStatus ? 'approved' : 'rejected'
            ]);
            $this->dispatch('vendorUpdateMS');
        }
    }

    public function deleteItem($id)
    {
        $vendor = Vendor::find($id);
        if ($vendor) {
            $vendor->delete();
            $this->dispatch('itemDeleted');
            $this->dispatch('refreshData');
        }
    }

    public function render()
    {
        $storeTypes = StoreType::query()->orderBy('name')->get(['id', 'name']);

        $data = Vendor::query()
            ->with(['storeType:id,name'])
            ->when($this->search, function ($query) {
                $query->where(function ($searchQuery) {
                    $searchQuery->where('store_name', 'like', '%' . $this->search . '%')
                        ->orWhere('owner_name', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->store_type_id, fn ($query) => $query->where('store_type_id', $this->store_type_id))
            ->latest()
            ->paginate(10);

        $data->getCollection()->transform(function (Vendor $vendor) {
            $vendor->effective_approval_status = $this->resolveEffectiveApprovalStatus($vendor);

            return $vendor;
        });

        $filteredCollection = $data->getCollection()->filter(function (Vendor $vendor) {
            $status = $vendor->effective_approval_status;

            if ($this->is_request_page) {
                if ($this->approval_status) {
                    return $status === $this->approval_status;
                }

                return in_array($status, ['pending', 'rejected'], true);
            }

            if ($this->approval_status) {
                return $status === $this->approval_status;
            }

            return $status === 'approved';
        })->values();

        $data->setCollection($filteredCollection);

        return view('dashboard.settings.vendors.vendor-data', compact('data', 'storeTypes'));
    }
}
