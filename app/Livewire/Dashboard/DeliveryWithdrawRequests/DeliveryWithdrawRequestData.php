<?php

namespace App\Livewire\Dashboard\DeliveryWithdrawRequests;

use App\Enums\DeliveryWithdrawalStatus;
use App\Models\DeliveryWithdrawalRequest;
use App\Services\DeliveryWalletService;
use Livewire\Component;
use Livewire\WithPagination;

class DeliveryWithdrawRequestData extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $adminNote = '';
    public $selectedRequestId = null;

    protected DeliveryWalletService $walletService;

    public function boot(DeliveryWalletService $walletService): void
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

    public function getSelectedRequest(): ?DeliveryWithdrawalRequest
    {
        if (!$this->selectedRequestId) {
            return null;
        }

        return DeliveryWithdrawalRequest::query()
            ->with(['delivery', 'processedByAdmin', 'orderAllocations.order'])
            ->find($this->selectedRequestId);
    }

    public function approve(int $requestId): void
    {
        $request = DeliveryWithdrawalRequest::findOrFail($requestId);

        $this->walletService->approveWithdrawalRequest(
            $request,
            auth('admin')->id(),
            $this->adminNote ?: null
        );

        $this->reset(['adminNote', 'selectedRequestId']);
        $this->dispatch('deliveryWithdrawRequestApproved');
    }

    public function reject(int $requestId): void
    {
        $request = DeliveryWithdrawalRequest::findOrFail($requestId);

        $this->walletService->rejectWithdrawalRequest(
            $request,
            auth('admin')->id(),
            $this->adminNote ?: null
        );

        $this->reset(['adminNote', 'selectedRequestId']);
        $this->dispatch('deliveryWithdrawRequestRejected');
    }

    public function render()
    {
        $data = DeliveryWithdrawalRequest::query()
            ->with(['delivery', 'processedByAdmin', 'orderAllocations.order'])
            ->when($this->status !== '', function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->search !== '', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery
                        ->where('transfer_details', 'like', '%' . $this->search . '%')
                        ->orWhere('method', 'like', '%' . $this->search . '%')
                        ->orWhereHas('delivery', function ($deliveryQuery) {
                            $deliveryQuery
                                ->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('phone', 'like', '%' . $this->search . '%')
                                ->orWhere('email', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->latest()
            ->paginate(10);

        $statuses = DeliveryWithdrawalStatus::cases();

        return view('dashboard.delivery-withdraw-requests.delivery-withdraw-request-data', compact('data', 'statuses'));
    }
}
