<?php

namespace App\Livewire\Dashboard\Users;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use App\Services\Dashboard\UserService;

class UserCreate extends Component
{
    use WithFileUploads;
    public $image, $name, $email, $phone, $password, $password_confirmation;
    protected $userService;
    protected $listeners = ['openUserCreateModal' => 'loadCountries'];


    public function boot(UserService $userService)
    {
        $this->userService = $userService;
    }

    protected function rules()
    {
        return [
            'image'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif'],
            'name'           => ['required', 'max:150'],
            'email'          => ['required', 'email', 'max:200', Rule::unique('users', 'email')],
            'phone'          => ['nullable', 'max:25'],
            'password'       => ['required', 'min:6', 'confirmed'],
        ];
    }

    public function submit()
    {
        $data = $this->validate();
        $user = $this->userService->create($data);

        if (!$user) {
            $this->dispatch('somethingFailed');
            return;
        }

        $this->dispatch('userAddMs');
        $this->reset('image', 'name', 'email', 'password', 'phone', 'password_confirmation');
        $this->dispatch('createModalToggle');
        $this->dispatch('refreshData')->to(UserData::class);
    }

    public function render()
    {
        return view('dashboard.users.user-create');
    }
}
