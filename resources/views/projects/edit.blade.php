@extends('layouts.app')

@section('title', 'Edit Project')

@section('content')
<div class="container">
    <h2>Edit Project</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('projects.update', $project->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Project Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $project->name) }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Project Description</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description', $project->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="start_date" class="form-label">Start Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $project->start_date) }}" required>
        </div>

        <div class="mb-3">
            <label for="end_date" class="form-label">End Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $project->end_date) }}" required>
        </div>

        <div class="mb-3">
            <label for="status_id" class="form-label">Status</label>
            <select name="status_id" class="form-select" required>
                @foreach ($statuses as $status)
                    <option value="{{ $status->id }}" {{ old('status_id', $project->status_id) == $status->id ? 'selected' : '' }}>
                        {{ $status->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button class="btn btn-success">Update</button>
        <a href="{{ route('projects.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
