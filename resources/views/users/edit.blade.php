@extends('layouts.app')

@section('title', 'Users | All Users')

@section('content')
<h2>Edit User</h2>

<form action="{{ route('users.update', $user->id) }}" method="POST">
    @csrf @method('PUT')

    <label>Name</label>
    <input type="text" name="name" value="{{ $user->name }}" class="form-input">

    <label>Email</label>
    <input type="email" name="email" value="{{ $user->email }}" class="form-input">

    <label>Roles</label>
    <select name="roles[]" multiple class="form-select">
        @foreach($roles as $role)
            <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                {{ $role->name }}
            </option>
        @endforeach
    </select>

    <button class="btn btn-primary">Save</button>
</form>
@endsection
