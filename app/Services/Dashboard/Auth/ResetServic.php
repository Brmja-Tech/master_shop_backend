<?php

namespace App\Services\Dashboard\Auth;

use App\Repositories\Dashboard\Auth\ResetRepository;

class ResetServic
{
    protected $resetRepository;
    public function __construct(ResetRepository $resetRepository)
    {
        $this->resetRepository = $resetRepository;
    } // End __construct()

    public function resetPassword($email , $password)
    {
        $admin = $this->resetRepository->resetPassword($email , $password);
        return $admin;
    } // End ResetPassword
}
