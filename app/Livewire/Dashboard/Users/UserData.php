<?php

namespace App\Livewire\Dashboard\Users;

use App\Services\Dashboard\UserService;
use Livewire\Component;
use Livewire\WithPagination;

class UserData extends Component
{
    use WithPagination;
    protected $listeners = ['refreshData' => '$refresh', 'deleteItem'];
    protected $userService;
    public $search;

    public function boot(UserService $userService)
    {
        $this->userService = $userService;
    }


    public function updatingSearch()
    {
        $this->resetPage();
    }


    public function updateStatus($itemId)
    {
        $this->userService->changestatus($itemId);
        $this->dispatch('userStatusUpdate');
    }



    public function deleteItem($id)
    {
        $this->userService->destroy($id);
        $this->dispatch('itemDeleted');
        $this->dispatch('refreshData');
    }

    public function render()
    {
        $data = $this->userService->getAllUsers($this->search);
        return view('dashboard.users.user-data', compact('data'));
    }
}
