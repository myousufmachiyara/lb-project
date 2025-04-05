@extends('layouts.app')

@section('title', 'Create Project')

@section('content')
<div class="container">
    <h2>Create New Project</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">Project Name</label>
            <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
        </div>
    
        <div class="mb-3">
            <label class="form-label">Account</label>
            <select name="acc_id" class="form-select" required>
                <option value="">-- Select Account --</option>
                @foreach ($accounts as $account)
                    <option value="{{ $account->id }}" {{ old('acc_id') == $account->id ? 'selected' : '' }}>
                        {{ $account->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status_id" class="form-select" required>
                <option value="">-- Select Status --</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->id }}" {{ old('status_id') == $status->id ? 'selected' : '' }}>
                        {{ $status->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Total Pieces</label>
            <input type="number" name="total_pcs" class="form-control" required value="{{ old('total_pcs') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Attachments</label>
            <input type="file" name="attachments[]" class="form-control" multiple>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">Create Project</button>
            <a href="{{ route('projects.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection