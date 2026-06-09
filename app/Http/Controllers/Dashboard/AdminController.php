<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use App\Services\Dashboard\AdminService;
use App\Services\Dashboard\RoleService;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected $adminService, $roleService;
    public function __construct(AdminService $adminService, RoleService $roleService)
    {
        $this->adminService = $adminService;
        $this->roleService = $roleService;
    } //End constructor method
    public function index()
    {
        $admins = $this->adminService->getAdmins();
        return view('dashboard.admins.index', compact('admins'));
    } // End index method


    public function create()
    {
        $roles = $this->roleService->getRoles();
        return view('dashboard.admins.create', compact('roles'));
    } // End create method

    public function store(AdminRequest $request)
    {
        $data = $request->only(['name', 'email', 'password', 'status', 'role_id']);
        $admin = $this->adminService->createAdmin($data);
        if (!$admin) {
            flash()->erorr(__('validation.something-valid'));
            return redirect()->back();
        }
        flash()->success(__('validation.successfully'));
        return redirect()->route('dashboard.admins.index');
    } // End store method


    public function edit(string $id)
    {
        $roles = $this->roleService->getRoles();
        $admin = $this->adminService->getAdmin($id);
        return view('dashboard.admins.update', compact('admin', 'roles'));
    }


    public function update(AdminRequest $request, string $id)
    {
        $data = $request->only(['name', 'email', 'password', 'status', 'role_id']);
        if (!$this->adminService->updateAdmin($data, $id)) {
            return redirect()->back()->with(['error' => __('validation.something-valid')]);
        }
        return redirect()->route('dashboard.admins.index')->with('success', __('validation.successfully'));
    } // End update method


    public function destroy(string $id)
    {
        $admin = $this->adminService->destroy($id);
        if (!$admin) {
            return back()->with(['error' => __('validation.something-valid')]);
        }
        return redirect()->route('dashboard.admins.index')->with('success', __('validation.successfully'));
    } // End destroy method

    public function changeStatus($id)
    {
        $admin = $this->adminService->changeStatus($id);
        if (!$admin) {
            return response()->json([
                'status' => false,
                'message' => __('validation.something-valid'),
            ]);
        }
        return response()->json([
            'status' => true,
            'new_status' => $admin->status,
        ]);
    } // End changeStatus method
}
