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
            <div class="col-md-12 text-end mb-3">
                <button class="btn btn-success" id="bulk-complete">Bulk Complete</button>
                <button class="btn btn-danger" id="bulk-delete-tasks">Bulk Delete</button>
            </div>
            <!-- <form method="GET" action="{{ route('tasks.filter') }}" class="mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <label for="date">Filter by Date:</label>
                        <input type="date" name="date" id="date" class="form-control" value="{{ request('date', date('Y-m-d')) }}">
                    </div>

                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form> -->
            <div class="modal-wrapper table-scroll" style="overflow-x: auto;">
                <table class="table table-bordered table-striped mb-0" id="cust-datatable-default">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="select-all-tasks"></th>
                            <th>S.NO</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Project</th>
                            <th>Repeat</th>
                            <th>Date / Time</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $item)
                            <tr>
                                <td><input type="checkbox" class="task-checkbox" name="task_ids[]" value="{{ $item->id }}"></td>
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
                                <td>
                                    @if($item->is_recurring)
                                        <span class="badge bg-success">Yes ({{ $item->recurring_frequency }} days)</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $item->next_due_date ? \Carbon\Carbon::parse($item->next_due_date)->format('Y-m-d') : 'N/A' }} / 
                                    {{ $item->due_time ? \Carbon\Carbon::createFromFormat('H:i:s', $item->due_time)->format('g:i A') : 'N/A' }}                                
                                </td>
                                <td>{{ $item->category->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge 
                                        @if($item->custom_status === 'Due') bg-danger
                                        @elseif($item->custom_status === 'In Progress') bg-warning
                                        @elseif($item->custom_status === 'Scheduled') bg-primary
                                        @elseif($item->custom_status === 'Unscheduled') bg-secondary
                                        @elseif($item->custom_status === 'Completed') bg-success
                                        @else bg-info @endif">
                                        {{ $item->custom_status }}
                                    </span>
                                </td>
                              
                                <td>{{ $item->description ?? 'N/A' }}</td>
                                <td>
                                    @if(!$item->last_completed_at || ($item->is_recurring && isset($item->next_due_date) && \Carbon\Carbon::parse($item->next_due_date)->lte(\Carbon\Carbon::today())))                                        <form action="{{ route('tasks.complete', $item->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="text-success bg-transparent border-0" title="Mark as Complete">
                                                <i class="fa fa-check"></i>
                                            </button>
                                        </form>
                                    @endif                                    
                                    <a href="javascript:void(0);" class="text-primary edit-task-btn" data-id="{{ $item->id }}"><i class="fa fa-edit"></i></a>
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
                            <label>Recurring</label>
                            <select name="is_recurring" class="form-control">
                                <option value="0" selected>No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>

                        <div class="col-lg-6 mb-2">
                            <label>Repeat Days</label>
                            <select name="recurring_frequency" class="form-control">
                                <option value="">Repeat After Every</option>
                                @for ($i = 1; $i <= 30; $i++)
                                    <option value="{{ $i }}">{{ $i }} days</option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-lg-6 mb-2">
                            <label>Due Date</label>
                            <input type="date" class="form-control" placeholder="Date" name="due_date" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label>Time</label>
                            <input type="time" class="form-control" placeholder="Time" name="due_time" value="{{ now()->format('H:i') }}">
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

      <div id="editModal" class="modal-block modal-block-primary mfp-hide">
            <section class="card">
                <form method="POST" id="editForm" action="" onkeydown="return event.key != 'Enter';">
                    @csrf
                    @method('PUT')
                    <header class="card-header">
                        <h2 class="card-title">Edit Task</h2>
                    </header>
                    <div class="card-body">
                        <input type="hidden" name="task_id" id="edit_task_id">

                        <div class="row form-group">
                            <div class="col-lg-6 mb-2">
                                <label>Task Name</label>
                                <input type="text" class="form-control" id="edit_task_name" name="task_name" required>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Recurring</label>
                                <select class="form-control" id="edit_is_recurring" name="is_recurring">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Repeat Days</label>
                                <select name="recurring_frequency" id="edit_recurring_frequency" class="form-control">
                                    <option value="">Repeat After Every</option>
                                    @for ($i = 1; $i <= 30; $i++)
                                        <option value="{{ $i }}">{{ $i }} days</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Due Date</label>
                                <input type="date" class="form-control" name="due_date" id="edit_due_date">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Time</label>
                                <input type="time" class="form-control" name="due_time" id="edit_due_time">
                            </div>  
                            <div class="col-lg-6 mb-2">
                                <label>Category</label>
                                <select class="form-control select2-js" id="edit_category_id" name="category_id">
                                    <option value="">Select Category</option>
                                    @foreach ($category as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Status</label>
                                <select class="form-control select2-js" id="edit_status_id" name="status_id">
                                    <option value="">Select Status</option>
                                    @foreach ($status as $statuses)
                                        <option value="{{ $statuses->id }}">{{ $statuses->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Project</label>
                                <select class="form-control select2-js" id="edit_project_id" name="project_id">
                                    <option value="">Select Project</option>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Description</label>
                                <textarea class="form-control" rows="2" id="edit_description" name="description"></textarea>
                            </div>
                           
                        </div>
                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">Update Task</button>
                                <button class="btn btn-default modal-dismiss">Cancel</button>
                            </div>
                        </div>
                    </footer>
                </form>
            </section>
      </div>

    </div>
  </div>
  <script>
    $(document).ready(function(){
        var table = $('#cust-datatable-default').DataTable(
            {
                "pageLength": 100,  // Show all rows
            }
        );

        $('#bulk-complete').on('click', function () {
            const ids = $('.task-checkbox:checked').map(function () {
                return this.value;
            }).get();

            if (!ids.length) {
                alert('Select tasks first.');
                return;
            }

            $.ajax({
                url: "{{ route('tasks.bulk-complete') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    task_ids: ids
                },
                success: function () {
                    location.reload();
                }
            });
        });

        $('#bulk-delete-tasks').on('click', function () {
            const ids = $('.task-checkbox:checked').map(function () {
                return this.value;
            }).get();

            if (!ids.length || !confirm('Are you sure you want to delete selected tasks?')) {
                return;
            }

            $.ajax({
                url: "{{ route('tasks.bulk-delete') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    task_ids: ids
                },
                success: function () {
                    location.reload();
                }
            });
        });
    });

    $('.edit-task-btn').on('click', function () {
        var taskId = $(this).data('id');
        $.ajax({
            url: `/tasks/${taskId}/edit`,
            type: 'GET',
            success: function (response) {
                console.log(response);
                $('#edit_task_id').val(response.id);
                $('#edit_task_name').val(response.task_name);
                if (response.due_date) {
                    const formattedDate = new Date(response.due_date).toISOString().split('T')[0];
                    $('#edit_due_date').val(formattedDate);
                } else {
                    $('#edit_due_date').val('');
                }  
                if (response.due_time) {
                    // Keep only HH:MM part (in case seconds are included)
                    const timeParts = response.due_time.split(':');
                    const formattedTime = timeParts[0].padStart(2, '0') + ':' + timeParts[1].padStart(2, '0');
                    $('#edit_due_time').val(formattedTime);
                } else {
                    $('#edit_due_time').val('');
                }                 
                $('#edit_description').val(response.description);
                $('#edit_category_id').val(response.category_id).trigger('change');
                $('#edit_status_id').val(response.status_id).trigger('change');
                $('#edit_project_id').val(response.project_id).trigger('change');
                $('#edit_is_recurring').val(response.is_recurring ? 1 : 0);
                $('#edit_recurring_frequency').val(response.recurring_frequency);

                // Set form action dynamically
                $('#editForm').attr('action', `/tasks/${taskId}`);

                // Show modal
                $.magnificPopup.open({
                    items: {
                        src: '#editModal'
                    },
                    type: 'inline'
                });
            },
            error: function () {
                alert('Failed to fetch task data.');
            }
        });
    });

    $('#select-all-tasks').on('click', function () {
        $('.task-checkbox').prop('checked', this.checked);
    });

  </script>
@endsection
