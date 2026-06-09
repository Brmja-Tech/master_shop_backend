<?php

namespace App\Repositories\Dashboard;

use App\Models\Admin;

class AdminRepository
{
    public function getAdmin($id)
    {
        $admin = Admin::find($id);
        if (!$admin) {
            return back()->with(['error' => __('validation.something-valid')]);
        }
        return $admin;
    } //End getRole method
    public function getAdmins()
    {
        return Admin::whereKeyNot(1)->latest()->paginate(10);
    } // End index method

    public function createAdmin($data)
    {
        $admin = Admin::create($data);
        return $admin;
    } // End createAdmin method

    public function updateAdmin($data, $admin)
    {
        $admin = $admin->update($data);
        return $admin;
    } // End updateAdmin method

    public function destroy($admin)
    {
        return  $admin->delete();
    } // End destroy method

    public function changestatus($admin) {
        if ($admin->id != 1) {
            $admin->status = $admin->status == 1 ? 0 : 1;
            $admin->save();
            return $admin;
        }
        return false;
    } // End changestatus method
}
