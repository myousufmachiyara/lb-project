@extends('layouts.app')

@section('title', 'Roles | Form')

@section('content')
<div class="container">
    <h2 class="mb-4">{{ isset($role) ? 'Edit' : 'Create' }} Role</h2>

    <form action="{{ isset($role) ? route('roles.update', $role->id) : route('roles.store') }}" method="POST">
        @csrf
        @if(isset($role)) @method('PUT') @endif

        <div class="form-group mb-3">
            <label for="name"><strong>Role Name</strong></label>
            <input type="text" name="name" value="{{ $role->name ?? '' }}" class="form-control" required>
        </div>

        <h5 class="mt-4 mb-2">Assign Permissions</h5>

        <table class="table table-bordered table-striped">
            <thead class="bg-primary text-white text-center">
                <tr>
                    <th>Module</th>
                    <th>View</th>
                    <th>Add</th>
                    <th>Edit</th>
                    <th>Delete</th>
                    <th>Print</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $groupedPermissions = [];

                    foreach ($permissions as $permission) {
                        $parts = explode('.', $permission->name);
                        if (count($parts) === 2) {
                            [$module, $action] = $parts;
                            $groupedPermissions[$module][$action] = $permission->name;
                        }
                    }

                    $actions = ['index' => 'Index', 'create' => 'Add', 'edit' => 'Edit', 'delete' => 'Delete', 'print' => 'Print'];
                @endphp


                @foreach($groupedPermissions as $module => $perms)
                    <tr>
                        <td><strong>{{ ucfirst($module) }}</strong></td>
                        @foreach($actions as $actionKey => $actionLabel)
                            <td class="text-center">
                                @if(isset($perms[$actionKey]))
                                    <input type="checkbox"
                                           name="permissions[]"
                                           value="{{ $perms[$actionKey] }}"
                                           {{ isset($role) && $role->hasPermissionTo($perms[$actionKey]) ? 'checked' : '' }}>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button class="btn btn-success mt-3">Save Role</button>
    </form>
</div>
@endsection
