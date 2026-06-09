<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Requests\RoleRequest;
use App\Http\Controllers\Controller;
use App\Services\Dashboard\RoleService;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    } // End ___construct

    public function index()
    {
        $roles = $this->roleService->getRoles();
        return view('dashboard.roles.index', compact('roles'));
    } //End index method

    public function create()
    {
        return view('dashboard.roles.create');
    } // End create method

    public function store(RoleRequest $request)
    {
        $role = $this->roleService->createRole($request);
        if (!$role) {
            return back()->with(['error' => __('validation.something-valid')]);
        }
        return redirect()->route('dashboard.roles.index')->with('success', __('validation.successfully'));
    } // End store method

    public function edit(string $id)
    {
        $role = $this->roleService->getRole($id);
        return view('dashboard.roles.update', compact('role'));
    } // End edit method

    public function update(RoleRequest $request, string $id)
    {
        $role = $this->roleService->updateRole($request, $id);
        if (!$role) {
            return back()->with(['error' => __('validation.something-valid')]);
        }
        return redirect()->route('dashboard.roles.index')->with('success', __('validation.successfully'));
    } // End update method

    public function destroy(string $id)
    {
        $role = $this->roleService->destroy($id);
        if (!$role) {
            return back()->with(['error' => __('validation.something-valid')]);
        }
        return redirect()->route('dashboard.roles.index')->with('success', __('validation.successfully'));
    } // End destroy method
}
