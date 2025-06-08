@extends('layouts.app')

@section('title', 'Roles | All Roles')

@section('content')

<h2>Roles</h2>
<a href="{{ route('roles.create') }}" class="btn btn-success">+ New</a>

<table class="table mt-3">
    <thead><tr><th>Name</th><th>Permissions</th><th>Actions</th></tr></thead>
    <tbody>
        @foreach($roles as $role)
        <tr>
            <td>{{ $role->name }}</td>
            <td>{{ $role->permissions->pluck('name')->join(', ') }}</td>
            <td>
                <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning btn-sm">Edit</a>
                <a href="{{ route('roles.permissions', $role->id) }}" class="btn btn-success">Assign</a>
                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button onclick="return confirm('Delete?')" class="btn btn-danger btn-sm">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
