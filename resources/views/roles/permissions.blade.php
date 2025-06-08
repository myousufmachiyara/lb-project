@extends('layouts.app')

@section('title', 'Assign Permissions')

@section('content')
<h2>Assign Permissions to Role: {{ $role->name }}</h2>

<form action="{{ route('admin.roles.assign-permissions', $role->id) }}" method="POST">
    @csrf
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Module</th>
                @foreach($actions as $action)
                    <th>{{ ucfirst($action) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($modules as $module)
                <tr>
                    <td>{{ $module->name }}</td>
                    @foreach($actions as $action)
                        @php
                            $perm = $module->shortcode . '.' . $action;
                        @endphp
                        <td class="text-center">
                            <input type="checkbox" name="permissions[]" value="{{ $perm }}"
                                {{ $role->hasPermissionTo($perm) ? 'checked' : '' }}>
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
    <button class="btn btn-primary">Save</button>
</form>
@endsection
