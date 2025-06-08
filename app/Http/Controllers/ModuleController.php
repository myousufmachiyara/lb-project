<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = Module::all();
        return view('modules.index', compact('modules'));
    }

    public function create()
    {
        return view('modules.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:modules,name',
            'shortcode' => 'required|unique:modules,shortcode',
            'status' => 'required|boolean',
        ]);

        // Create module
        $module = Module::create($request->only('name', 'shortcode', 'status'));

        // Define your actions
        $actions = ['create', 'edit', 'delete', 'view', 'print'];

        // Create permissions dynamically for this module
        foreach ($actions as $action) {
            $permissionName = "{$module->shortcode}.{$action}";

            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        return redirect()->route('modules.index')->with('success', 'Module and permissions created successfully.');
    }

    public function edit($id)
    {
        $module = Module::findOrFail($id);
        return view('modules.edit', compact('module'));
    }

    public function update(Request $request, $id)
    {
        $module = Module::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:modules,name,' . $module->id,
            'shortcode' => 'required|string|max:50|unique:modules,shortcode,' . $module->id,
            'status' => 'required|boolean',
        ]);

        $module->update($request->all());

        return redirect()->route('modules.index')->with('success', 'Module updated successfully.');
    }

    public function destroy($id)
    {
        $module = Module::findOrFail($id);
        $module->delete();

        return redirect()->route('modules.index')->with('success', 'Module deleted successfully.');
    }
}
