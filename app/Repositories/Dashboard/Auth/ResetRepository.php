<?php

namespace App\Repositories\Dashboard\Auth;

use App\Models\Admin;

class ResetRepository
{
    public function resetPassword($email , $password){
        $admin = Admin::where('email', $email)->first();
        if(!$admin){
            return false;
        }
        $admin->update(['password' => bcrypt($password)]);
        return $admin;
    } // End ResetPassword
}
