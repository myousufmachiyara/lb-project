@extends('layouts.app')

@section('title', 'Edit Project')

@section('content')
    <div class="row">
        <div class="col">
            <form action="{{ route('projects.update', $project->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
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
                                                    style="width: 100px; overflow: hidden;">
                                                    <a href="{{ asset($attachment->att_path) }}" data-plugin-lightbox="" data-plugin-options="{ &quot;type&quot;:&quot;image&quot; }" title="{{ $project->name }}">
                                                        <img class="img-fluid" src="{{ asset($attachment->att_path) }}" alt="Project Image" style="object-fit: cover; border-radius: 6px;">
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
                    </div>
                </section>
                @if ($project->tasks->count())
                    <section class="card mt-4">
                        <header class="card-header">
                            <div style="display: flex;justify-content: space-between;">
                                <h2 class="card-title">Project Task</h2>
                            </div>
                            @if ($errors->has('error'))
                                <strong class="text-danger">{{ $errors->first('error') }}</strong>
                            @endif
                        </header>

                        <div class="card-body" style="max-height:400px; overflow-y:auto">
                            <table class="table table-bordered" id="myTable">
                                <thead>
                                    <tr>
                                        <th width="2%">Task</th>
                                        <th>Description</th>
                                        <th>Date</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="ProjectTaskTable">
                                    @foreach ($project->tasks as $i => $task)
                                    <tr>
                                        <input type="hidden" name="tasks[{{ $i }}][id]" value="{{ $task->id }}">
                                        <td width="25%">
                                            <input type="text" name="tasks[{{ $i }}][task_name]" class="form-control" placeholder="Task"
                                                value="{{ $task->task_name }}" />
                                        </td>
                                        <td><input type="text" name="tasks[{{ $i }}][description]" class="form-control" placeholder="Description"
                                                value="{{ $task->description }}" /></td>
                                        <td><input type="date" name="tasks[{{ $i }}][due_date]" class="form-control"
                                                value="{{ $task->due_date ? $task->due_date->format('Y-m-d') : '' }}" /></td>
                                        <td>
                                            <select data-plugin-selecttwo class="form-control select2-js" name="tasks[{{ $i }}][category_id]">
                                                <option value="" selected disabled>Task Category</option>
                                                @foreach ($taskCat as $cat)
                                                    <option value="{{ $cat->id }}" {{ $task->category_id == $cat->id ? 'selected' : '' }}>
                                                        {{ $cat->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select data-plugin-selecttwo class="form-control select2-js" name="tasks[{{ $i }}][status_id]">
                                                <option value="" selected disabled>Task Status</option>
                                                @foreach ($statuses as $status)
                                                    <option value="{{ $status->id }}" {{ $task->status_id == $status->id ? 'selected' : '' }}>
                                                        {{ $status->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <button type="button" onclick="removeRow(this)" class="btn btn-danger" tabindex="1"><i class="fas fa-times"></i></button>
                                            <button type="button" class="btn btn-primary" onclick="addNewRow()"><i class="fa fa-plus"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                @endif
                <footer class="card-footer text-end mt-2">
                    <a class="btn btn-danger" href="{{ route('projects.index') }}">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </footer>

            </form>
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
    <script>
        let index = {{ count($project->tasks) }};

        function removeRow(button) {
            let tableRows = $("#ProjectTaskTable tr").length;
            if (tableRows > 1) {
                let row = button.parentNode.parentNode;
                row.parentNode.removeChild(row);
                index--;
            }
        }

        function addNewRow() {
            let lastRow = $('#ProjectTaskTable tr:last');
            let latestValue = lastRow[0].cells[0].querySelector('input').value;

            if (latestValue != "") {
                let table = document.getElementById('myTable').getElementsByTagName('tbody')[0];
                let newRow = table.insertRow(table.rows.length);

                newRow.insertCell(0).innerHTML = '<input type="text" name="tasks['+index+'][task_name]" class="form-control" placeholder="Task Name" required/>';
                newRow.insertCell(1).innerHTML = '<input type="text" name="tasks['+index+'][description]" class="form-control" placeholder="Description"/>';
                newRow.insertCell(2).innerHTML = '<input type="date" name="tasks['+index+'][due_date]" class="form-control" />';
                newRow.insertCell(3).innerHTML = `<select data-plugin-selecttwo class="form-control select2-js" name="tasks[${index}][category_id]">
                    <option value="" disabled selected>Select Category</option>
                    @foreach ($taskCat as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>`;
                newRow.insertCell(4).innerHTML = `<select data-plugin-selecttwo class="form-control select2-js" name="tasks[${index}][status_id]">
                    <option value="" disabled selected>Select Status</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                    @endforeach
                </select>`;
                newRow.insertCell(5).innerHTML = '<button type="button" onclick="removeRow(this)" class="btn btn-danger" tabindex="1"><i class="fas fa-times"></i></button>' +
                                                ' <button type="button" class="btn btn-primary" onclick="addNewRow()"><i class="fa fa-plus"></i></button>';

                index++;
                $('#myTable select[data-plugin-selecttwo]').select2();
            }
        }
    </script>

@endsection

