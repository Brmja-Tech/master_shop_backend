<?php

namespace App\Services\Dashboard;

use App\Repositories\Dashboard\AdminRepository;

class AdminService
{
    protected $adminRepository;
    public function __construct(AdminRepository $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }


    public function getAdmin($id)
    {
        $admin = $this->adminRepository->getAdmin($id);
        if (!$admin) {
            return back()->with(['error' => __('validation.something-valid')]);
        }
        return $admin;
    } // End getAdmin method


    public function getAdmins()
    {
        $admin = $this->adminRepository->getAdmins();
        return $admin;
    } //End method index


    public function createAdmin($data)
    {
        $admin = $this->adminRepository->createAdmin($data);
        if (!$admin) {
            return false;
        }
        return $admin;
    } // End method create


    public function updateAdmin($data, $id)
    {
        $admin = $this->adminRepository->getAdmin($id);
        if (!$admin) {
            abort(404);
        }
        if ($admin->id == 1) {
            return $admin;
        }
        if ($data['password'] == null || $admin->id == 1) {
            unset($data['password']);
        }

        $admin = $this->adminRepository->updateAdmin($data, $admin);
        if (!$admin) {
            return false;
        }
        return $admin;
    } // End updateAdmin method


    public function destroy($id)
    {
        $admin = $this->adminRepository->getAdmin($id);
        if (!$admin) {
            abort(404);
        }
        if ($admin->id == auth('admin')->user()->id || $admin->id == 1) {
            return false;
        } else {
            $admin = $this->adminRepository->destroy($admin);
        }
        if (!$admin) {
            return false;
        }
        return $admin;
    } // End destroy method

    public function changestatus($id)
    {
        $admin = $this->adminRepository->getAdmin($id);
        $admin = $this->adminRepository->changestatus($admin);
        return $admin;
    } // End changeStatus method
}
