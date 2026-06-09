<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\UserService;

class UserController extends Controller
{

    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }



    public function index()
    {
        return view('dashboard.users.index');
    }


    public function userProfile($id)
    {
        $user = $this->userService->getUser($id);

        if (!$user instanceof \App\Models\User) {
            return redirect()->route('users.index')->with('error', __('dashboard.user-not-found'));
        }

        return view('dashboard.users.profile', compact(['user']));
    }

}
