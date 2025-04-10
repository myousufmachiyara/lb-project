@extends('layouts.app')

@section('title', 'Projects | New Project')

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
                <header class="card-header" style="display: flex;justify-content: space-between;">
                    <h2 class="card-title">Add New Project</h2>
                </header>
                <div class="card-body">
                    <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data" id="project-form">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-md-3 mb-3">
                                <label class="form-label">Project Name</label>
                                <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                            </div>
                            
                            <div class="col-12 col-md-2 mb-3">
                                <label class="form-label">Total Pieces</label>
                                <input type="number" name="total_pcs" class="form-control" required value="{{ old('total_pcs') }}">
                            </div>
                            <div class="col-12 col-md-3 mb-3">
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
                            <div class="col-12 col-md-4 mb-3">
                                <label class="form-label">Attachments</label>
                                <input type="file" name="attachments[]" class="form-control" required multiple id="attachments">
                            </div>
                            <div class="col-12 col-md-5 mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                            </div>
                            
                            <footer class="card-footer text-end mt-2">
                                <a class="btn btn-danger" href="{{ route('projects.index') }}">Discard</a>
                                <button type="submit" class="btn btn-primary">Create</button>
                            </footer>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>

    <script>
        document.getElementById('attachments').addEventListener('change', function (event) {
            const files = event.target.files;
            const formData = new FormData();

            Array.from(files).forEach((file, index) => {
                if (!file.type.startsWith('image/')) return;

                const reader = new FileReader();
                reader.readAsDataURL(file);

                reader.onload = function () {
                    const img = new Image();
                    img.src = reader.result;

                    img.onload = function () {
                        const canvas = document.createElement('canvas');
                        canvas.width = img.width;
                        canvas.height = img.height;

                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0);

                        canvas.toBlob(blob => {
                            formData.append('attachments[]', blob, file.name);

                            // When all files are processed, submit the form
                            if (formData.getAll('attachments[]').length === files.length) {
                                uploadForm(formData);
                            }
                        }, 'image/jpeg', 0.7); // 70% quality
                    };
                };
            });
        });

        function uploadForm(formData) {
            // Submit form via AJAX
            fetch('{{ route('projects.store') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                alert('Project created successfully!');
                window.location.href = '{{ route('projects.index') }}'; // Redirect to projects index
            })
            .catch(error => {
                alert('Upload failed!');
                console.error(error);
            });
        }
    </script>
@endsection
