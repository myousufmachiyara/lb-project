@extends('layouts.app')

@section('title', 'Task | All Task')

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
        <header class="card-header">
            <div style="display: flex;justify-content: space-between;">
                <h2 class="card-title">All Task</h2>
                <div>
                    <button type="button" class="modal-with-form btn btn-primary" href="#addModal"> <i class="fas fa-plus"></i> Add Task</button>
                </div>
            </div>
        </header>
       
        <div class="card-body">
          <div class="modal-wrapper table-scroll">
                <table class="table table-bordered table-striped mb-0" id="cust-datatable-default">
                    <thead>
                        <tr>
                            <th>S.NO</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Project</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @php
                                        $imageAttachment = $item->project?->attachments->firstWhere(function ($att) {
                                            $ext = strtolower(pathinfo($att->att_path, PATHINFO_EXTENSION));
                                            return in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
                                        });
                                    @endphp

                                    @if ($imageAttachment)
                                        <a href="{{ asset($imageAttachment->att_path) }}" data-plugin-lightbox="" data-plugin-options="{ &quot;type&quot;:&quot;image&quot; }" title="{{ $item->project->name ?? 'Project' }}">
                                            <img class="img-fluid" src="{{ asset($imageAttachment->att_path) }}" alt="Project Image" width="60" height="60" style="object-fit: cover; border-radius: 6px;">
                                        </a>
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>

                                <td>{{ $item->task_name }}</td>
                                <td>{{ $item->project->name ?? 'N/A' }}</td>
                                <td>{{ $item->category->name ?? 'N/A' }}</td>
                                <td>{{ $item->status->name ?? 'N/A' }}</td>
                                <td>{{ $item->description ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('tasks.edit', $item->id) }}" class="text-primary"><i class="fa fa-edit"></i></a>
                                    <form action="{{ route('tasks.destroy', $item->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-danger bg-transparent" style="border:none" onclick="return confirm('Are you sure?')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>                                       
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
          </div>
        </div>
      </section>

      <div id="addModal" class="modal-block modal-block-primary mfp-hide">
        <section class="card">
            <form method="post" id="addForm" action="{{ route('tasks.store') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                @csrf
                <header class="card-header">
                    <h2 class="card-title">Add New Task</h2>
                </header>
                <div class="card-body">
                    <div class="row form-group">
                        <div class="col-lg-6 mb-2">
                            <label>Task Name<span style="color: red;"><strong>*</strong></span></label>
                            <input type="text" class="form-control" placeholder="Task Name" name="task_name" required>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label>Due Date</label>
                            <input type="date" class="form-control" placeholder="Date" name="due_date" value="<?php echo date('Y-m-d'); ?>">
                        </div>  
                        <div class="col-lg-6 mb-2">
                            <label>Category</label>
                            <select data-plugin-selecttwo class="form-control select2-js"  name="category_id">
                                <option value="0" disabled selected>Select Category</option>
                                @foreach ($category as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label>Status</label>
                            <select data-plugin-selecttwo class="form-control select2-js"  name="status_id">
                                <option value="0" disabled selected>Select Status</option>
                                @foreach ($status as $statuses)
                                    <option value="{{ $statuses->id }}" {{ old('status_id') == $statuses->id ? 'selected' : '' }}>
                                        {{ $statuses->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-lg-6 mb-2">
                            <label>Project</label>
                            <select data-plugin-selecttwo class="form-control select2-js"  name="project_id">
                                <option value="0" disabled selected>Select Project</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label>Description</label>
                            <textarea type="text" class="form-control" rows="2" placeholder="Description" name="description"></textarea>
                        </div>
                    </div>
                </div>
                <footer class="card-footer">
                    <div class="row">
                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-primary">Add Task</button>
                            <button class="btn btn-default modal-dismiss">Cancel</button>
                        </div>
                    </div>
                </footer>
            </form>
        </section>
      </div>
    </div>
  </div>
@endsection
