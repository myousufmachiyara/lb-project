<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Controller;
use App\Models\Module;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('roles.form', ['permissions' => $permissions]);
    }

    public function showPermissionsForm($id)
    {
        $role = Role::findOrFail($id);
        $modules = Module::all(); // assuming you have a Module model
        $actions = ['index', 'create', 'edit', 'delete', 'view' , 'print'];

        return view('roles.assign-permissions', compact('role', 'modules', 'actions'));
    }

    public function assignPermissionsForm(Role $role)
    {
        $modules = Module::all();
        $actions = ['index', 'create', 'edit', 'delete', 'view' , 'print'];

        return view('roles.permissions', compact('role', 'modules', 'actions'));
    }

    public function assignPermissions(Request $request, Role $role)
    {
        $permissions = $request->input('permissions', []);
        $role->syncPermissions($permissions);

        return redirect()->route('roles.index')->with('success', 'Permissions updated successfully.');
    }

    public function store(Request $request)
    {
        $role = Role::create(['name' => $request->name]);
        $role->givePermissionTo($request->permissions);
        return redirect()->route('roles.index');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        return view('roles.form', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);
        return redirect()->route('roles.index');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->back();
    }
}
