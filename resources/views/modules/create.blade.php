@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Add New Module</h2>

    <form action="{{ route('modules.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Module Name</label>
            <input type="text" name="name" class="form-control" required>
            @error('name')<div class="text-danger">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="shortcode" class="form-label">Shortcode</label>
            <input type="text" name="shortcode" class="form-control" required>
            @error('shortcode')<div class="text-danger">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control" required>
                <option value="1">Enabled</option>
                <option value="0">Disabled</option>
            </select>
            @error('status')<div class="text-danger">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn btn-success">Create</button>
        <a href="{{ route('modules.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
