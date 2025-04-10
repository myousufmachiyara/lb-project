@extends('layouts.app')

@section('title', 'Edit Project')

@section('content')
    <div class="row">
        <div class="col">
            <section class="card">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @elseif (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <header class="card-header" style="display: flex; justify-content: space-between;">
                    <h2 class="card-title">Edit Project</h2>
                </header>

                <div class="card-body">
                    <form action="{{ route('projects.update', $project->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-12 col-md-3 mb-3">
                                <label class="form-label">Project Name</label>
                                <input type="text" name="name" class="form-control" required value="{{ old('name', $project->name) }}">
                            </div>

                            <div class="col-12 col-md-2 mb-3">
                                <label class="form-label">Total Pieces</label>
                                <input type="number" name="total_pcs" class="form-control" required value="{{ old('total_pcs', $project->total_pcs) }}">
                            </div>

                            <div class="col-12 col-md-3 mb-3">
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

                            <div class="col-12 col-md-4 mb-3">
                                <label class="form-label">Add New Attachments</label>
                                <input type="file" name="attachments[]" class="form-control" required multiple>
                            </div>

                            <div class="col-12 col-md-5 mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4">{{ old('description', $project->description) }}</textarea>
                            </div>

                            @if ($project->attachments->count())
                                <div class="col-12 mt-3">
                                    <strong>Existing Attachments:</strong>
                                    <div class="d-flex flex-wrap gap-3 mt-2">
                                        @foreach ($project->attachments as $attachment)
                                            @php
                                                $ext = pathinfo($attachment->att_path, PATHINFO_EXTENSION);
                                                $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                            @endphp

                                            @if (in_array(strtolower($ext), $imageExts))
                                                <div class="border rounded" style="width: 100px; height: 100px; overflow: hidden;">
                                                    <img src="{{ asset('public/storage/' . $attachment->att_path) }}" 
                                                        alt="Attachment" 
                                                        style="width: 100%; height: 100%; object-fit: cover;">
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <footer class="card-footer text-end mt-2">
                            <a class="btn btn-danger" href="{{ route('projects.index') }}">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </footer>
                    </form>
                </div>
            </section>
        </div>
    </div>
@endsection
