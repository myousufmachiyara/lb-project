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

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('projects.update', $project->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Project Name</label>
            <input type="text" name="name" class="form-control" required value="{{ old('name', $project->name) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description', $project->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Account</label>
            <select name="acc_id" class="form-select" required>
                <option value="">-- Select Account --</option>
                @foreach ($accounts as $account)
                    <option value="{{ $account->id }}" {{ old('acc_id', $project->acc_id) == $account->id ? 'selected' : '' }}>
                        {{ $account->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Total Pieces</label>
            <input type="number" name="total_pcs" class="form-control" required value="{{ old('total_pcs', $project->total_pcs) }}">
        </div>

        <!-- Status Field -->
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status_id" class="form-select" required>
                <option value="">-- Select Status --</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->id }}" {{ old('status_id', $project->status_id) == $status->id ? 'selected' : '' }}>
                        {{ $status->name }}
                    </option>
                @endforeach
            </select>
        </div>

        @if ($project->attachments->count())
            <div class="mt-3">
                <strong>Existing Attachments:</strong>
                <div class="d-flex flex-wrap gap-3 mt-2">
                    @foreach ($project->attachments as $attachment)
                        @php
                            $ext = pathinfo($attachment->att_path, PATHINFO_EXTENSION);
                            $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        @endphp

                        @if (in_array(strtolower($ext), $imageExts))
                            <div class="border rounded" style="width: 150px; height: 150px; overflow: hidden;">
                                <img src="{{ asset('storage/' . $attachment->att_path) }}" 
                                     alt="Attachment" 
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mb-3 mt-4">
            <label class="form-label">Add New Attachments</label>
            <input type="file" name="attachments[]" class="form-control" multiple>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">Update Project</button>
            <a href="{{ route('projects.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
