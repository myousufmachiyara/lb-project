@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Module</h2>

    <form action="{{ route('modules.update', $module->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Module Name</label>
            <input type="text" name="name" class="form-control" value="{{ $module->name }}" required>
            @error('name')<div class="text-danger">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="shortcode" class="form-label">Shortcode</label>
            <input type="text" name="shortcode" class="form-control" value="{{ $module->shortcode }}" required>
            @error('shortcode')<div class="text-danger">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control" required>
                <option value="1" {{ $module->status ? 'selected' : '' }}>Enabled</option>
                <option value="0" {{ !$module->status ? 'selected' : '' }}>Disabled</option>
            </select>
            @error('status')<div class="text-danger">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('modules.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
