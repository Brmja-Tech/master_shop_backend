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
    public $active_status = '';
    public $ban_status = '';
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
        $vendor = Vendor::find($id);
        if ($vendor) {
            $vendor->update([
                'ban' => !$vendor->ban
            ]);
            $this->dispatch('vendorUpdateMS');
        }
    }

    public function toggleActive($id)
    {
        $vendor = Vendor::find($id);
        if ($vendor) {
            $vendor->update([
                'is_active' => !$vendor->is_active
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
            ->when($this->active_status !== '', function ($query) {
                $query->where('is_active', $this->active_status);
            })
            ->when($this->ban_status !== '', function ($query) {
                $query->where('ban', $this->ban_status);
            })
            ->when($this->is_request_page, function ($query) {
                if ($this->approval_status !== '') {
                    if ($this->approval_status === 'pending') {
                        $query->where(function($q) {
                            $q->where('is_verified', false)
                              ->where(function($sub) {
                                  $sub->where('approval_status', 'pending')
                                      ->orWhereNull('approval_status');
                              });
                        });
                    } elseif ($this->approval_status === 'rejected') {
                        $query->where('is_verified', false)->where('approval_status', 'rejected');
                    } elseif ($this->approval_status === 'approved') {
                        $query->where('is_verified', true);
                    }
                } else {
                    $query->where(function($q) {
                        $q->where('is_verified', false)
                          ->where(function($sub) {
                              $sub->whereIn('approval_status', ['pending', 'rejected'])
                                  ->orWhereNull('approval_status');
                          });
                    });
                }
            }, function ($query) {
                if ($this->approval_status !== '') {
                    if ($this->approval_status === 'approved') {
                        $query->where(function($q) {
                            $q->where('is_verified', true)
                              ->orWhere('approval_status', 'approved');
                        });
                    } elseif ($this->approval_status === 'pending') {
                        $query->where(function($q) {
                            $q->where('is_verified', false)
                              ->where(function($sub) {
                                  $sub->where('approval_status', 'pending')
                                      ->orWhereNull('approval_status');
                              });
                        });
                    } elseif ($this->approval_status === 'rejected') {
                        $query->where('is_verified', false)->where('approval_status', 'rejected');
                    }
                } else {
                    $query->where(function($q) {
                        $q->where('is_verified', true)
                          ->orWhere('approval_status', 'approved');
                    });
                }
            })
            ->latest()
            ->paginate(10);

        $data->getCollection()->transform(function (Vendor $vendor) {
            $vendor->effective_approval_status = $this->resolveEffectiveApprovalStatus($vendor);

            return $vendor;
        });

        return view('dashboard.settings.vendors.vendor-data', compact('data', 'storeTypes'));
    }
}
