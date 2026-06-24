<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class Sidebar extends Component
{
    protected $listeners = [
        'refreshData' => '$refresh',
        'refresh' => '$refresh',
        'withdrawRequestApproved' => '$refresh',
        'withdrawRequestRejected' => '$refresh',
        'deliveryWithdrawRequestApproved' => '$refresh',
        'deliveryWithdrawRequestRejected' => '$refresh',
        'userAddMs' => '$refresh',
        'vendorAddMS' => '$refresh',
        'vendorUpdateMS' => '$refresh',
        'userStatusUpdate' => '$refresh',
        'banToggled' => '$refresh',
        'itemDeleted' => '$refresh',
    ];

    public function render()
    {
        return view('dashboard.partials.sidebar');
    }
}
