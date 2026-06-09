<?php

namespace App\Services\Dashboard;

use App\Repositories\Dashboard\RoleRepository;

class RoleService
{
    protected $roleRepository;
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    } // End constructor

    public function getRole($id)
    {
        $role = $this->roleRepository->getRole($id);
        if (!$role) {
            return back()->withErrors(['error' => __('validation.something-valid')]);
        }
        return $role;
    } // End getRole method
    public function getRoles()
    {
        $roles = $this->roleRepository->getRoles();
        return $roles;
    } //End method index

    public function createRole($request)
    {
        $role = $this->roleRepository->createRole($request);
        return $role;
    } // End method create

    public function updateRole($request, $id)
    {
        $role = $this->roleRepository->getRole($id);
        if ($role->id == 1) {
            return $role;
        }
        if (!$role) {
            return false;
        }
        if ($role->id)
            return $this->roleRepository->updateRole($request, $role);
    } // End updateRole method

    public function destroy($id)
    {
        $role = $this->roleRepository->getRole($id);

        if ($role->id == 1) {
            return false;
        }
        if ($role->admins->count() > 0 || !$role) {
            return false;
        }
        return $this->roleRepository->destroy($role);
    } // End destroy method
}
