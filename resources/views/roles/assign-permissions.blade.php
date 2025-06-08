@extends('layouts.app')

@section('title', 'Assign Permissions')

@section('content')
<div class="container">
    <h2 class="mb-4">Assign Permissions to Role: <strong>{{ $role->name }}</strong></h2>

        <form action="{{ route('roles.assign-permissions', $role->id) }}" method="POST">
        @csrf

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Module</th>
                    @foreach($actions as $action)
                        <th class="text-center">{{ ucfirst($action) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($modules as $module)
                    <tr>
                        <td><strong>{{ $module->name }}</strong></td>
                        @foreach($actions as $action)
                            @php
                                $perm = $module->shortcode . '.' . $action;
                            @endphp
                            <td class="text-center">
                                <input 
                                    type="checkbox" 
                                    name="permissions[]" 
                                    value="{{ $perm }}" 
                                    {{ $role->hasPermissionTo($perm) ? 'checked' : '' }}
                                />
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary mt-3">Save Permissions</button>
    </form>
</div>
@endsection
