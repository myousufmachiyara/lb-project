@extends('layouts.app')

@section('title', 'Users | All Users')

@section('content')
<div class="container">
    <h2 class="d-flex justify-content-between align-items-center">
        Users
        <a href="{{ route('users.create') }}" class="btn btn-success btn-sm">+ Add New User</a>
    </h2>

    <table class="table mt-3">
        <thead>
            <tr>
                <th>Name</th><th>Email</th><th>Roles</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>    
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->roles->pluck('name')->join(', ') }}</td>
                <td>
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
