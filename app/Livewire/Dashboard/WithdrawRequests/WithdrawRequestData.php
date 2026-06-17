<?php

namespace App\Livewire\Dashboard\WithdrawRequests;

use App\Enums\VendorWithdrawalStatus;
use App\Models\VendorWithdrawalRequest;
use App\Services\VendorWalletService;
use Livewire\Component;
use Livewire\WithPagination;

class WithdrawRequestData extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $adminNote = '';
    public $selectedRequestId = null;

    protected VendorWalletService $walletService;

    public function boot(VendorWalletService $walletService): void
    {
        $this->walletService = $walletService;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function selectRequest(int $requestId): void
    {
        $this->selectedRequestId = $requestId;
        $this->adminNote = '';
    }

    public function getSelectedRequest(): ?VendorWithdrawalRequest
    {
        if (!$this->selectedRequestId) {
            return null;
        }

        return VendorWithdrawalRequest::query()
            ->with(['vendor', 'processedByAdmin', 'orderAllocations.order'])
            ->find($this->selectedRequestId);
    }

    public function approve(int $requestId): void
    {
        $request = VendorWithdrawalRequest::findOrFail($requestId);

        $this->walletService->approveWithdrawalRequest(
            $request,
            auth('admin')->id(),
            $this->adminNote ?: null
        );

        $this->reset(['adminNote', 'selectedRequestId']);
        $this->dispatch('withdrawRequestApproved');
    }

    public function reject(int $requestId): void
    {
        $request = VendorWithdrawalRequest::findOrFail($requestId);

        $this->walletService->rejectWithdrawalRequest(
            $request,
            auth('admin')->id(),
            $this->adminNote ?: null
        );

        $this->reset(['adminNote', 'selectedRequestId']);
        $this->dispatch('withdrawRequestRejected');
    }

    public function render()
    {
        $data = VendorWithdrawalRequest::query()
            ->with(['vendor', 'processedByAdmin', 'orderAllocations.order'])
            ->when($this->status !== '', function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->search !== '', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery
                        ->where('transfer_details', 'like', '%' . $this->search . '%')
                        ->orWhere('method', 'like', '%' . $this->search . '%')
                        ->orWhereHas('vendor', function ($vendorQuery) {
                            $vendorQuery
                                ->where('store_name', 'like', '%' . $this->search . '%')
                                ->orWhere('owner_name', 'like', '%' . $this->search . '%')
                                ->orWhere('phone', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->latest()
            ->paginate(10);

        $statuses = VendorWithdrawalStatus::cases();

        return view('dashboard.withdraw-requests.withdraw-request-data', compact('data', 'statuses'));
    }
}
