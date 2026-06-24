<?php

namespace App\Repositories\Dashboard;

use App\Models\Role;

class RoleRepository
{
    public function getRole($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return back()->withErrors(['error' => __('validation.something-valid')]);
        }
        return $role;
    } //End getRole method

    public function getRoles()
    {
        $roles = Role::latest()->paginate(5);
        return $roles;
    } // End index method

    public function createRole($request)
    {
        $role = Role::create([
            'role' => [
                'ar' => $request->role['ar'],
                'en' => $request->role['en']
            ],
            'permession' => json_encode($request->permession)
        ]);
        return $role;
    } // End createRole method

    public function updateRole($request, $role)
    {
        $role = $role->update([
            'role' => [
                'ar' => $request->role['ar'],
                'en' => $request->role['en']
            ],
            'permession' => json_encode($request->permession)
        ]);
        return $role;
    } // End updateRole method

    public function destroy($role)
    {
        return  $role->delete();
    } // End destroy method
}
