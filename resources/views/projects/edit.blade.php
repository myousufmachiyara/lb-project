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
                                <label class="form-label">Attachments</label>
                                <input type="file" name="attachments[]" class="form-control" multiple>
                            </div>

                            <div class="col-12 col-md-5 mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4" required                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              >{{ old('description', $project->description) }}</textarea>
                            </div>
                            <input type="hidden" name="kept_attachments" id="keptAttachments" value="{{ implode(',', $keptAttachmentIds) }}">

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
                                                <div class="attachment-wrapper position-relative border rounded"
                                                    data-id="{{ $attachment->id }}"
                                                    style="width: 100px; height: 100px; overflow: hidden;">
                                                    <a href="{{ asset($attachment->att_path) }}" data-plugin-lightbox="" data-plugin-options="{ &quot;type&quot;:&quot;image&quot; }" title="{{ $project->name }}">
                                                        <img class="img-fluid" src="{{ asset($attachment->att_path) }}" alt="Project Image" width="60" height="60" style="object-fit: cover; border-radius: 6px;">
                                                    </a>

                                                    <!-- ❌ Remove Button (Visual Only) -->
                                                    <button type="button"
                                                            class="btn btn-sm btn-danger position-absolute remove-attachment-btn"
                                                            style="top: 2px; right: 2px; z-index: 10; padding: 0 6px; line-height: 1;">
                                                        &times;
                                                    </button>
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
    <script>
   document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.remove-attachment-btn').forEach(button => {
        button.addEventListener('click', function () {
            const wrapper = this.closest('.attachment-wrapper');
            const attachmentId = wrapper.getAttribute('data-id');  // Get the ID of the attachment

            // Remove the attachment visually (fade-out effect)
            wrapper.classList.add('fade-out');
            setTimeout(() => wrapper.remove(), 300);

            // Update the kept_attachments hidden input field
            let keptAttachments = document.getElementById('keptAttachments').value;
            keptAttachments = keptAttachments ? keptAttachments.split(',') : [];  // Convert the value to an array

            // Remove the attachment ID from the list of kept attachments
            keptAttachments = keptAttachments.filter(id => id !== attachmentId);

            // Update the hidden input value
            document.getElementById('keptAttachments').value = keptAttachments.join(',');
        });
    });
});


        function compressImage(file) {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = () => {
                const img = new Image();
                img.src = reader.result;
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    canvas.width = img.width;
                    canvas.height = img.height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0);
                    canvas.toBlob(blob => {
                        // Upload this blob to Laravel backend
                    }, 'image/jpeg', 0.7); // compress to 70% quality
                };
            };
        }

    </script>
    <style>
        .fade-out {
            opacity: 0;
            transition: opacity 0.3s ease-out;
        }
    </style>
@endsection

