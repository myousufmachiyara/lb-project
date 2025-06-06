@extends('layouts.app')

@section('title', 'Projects | New Project')

@section('content')
  <div class="row">
    <div class="col">
      <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
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
              <h2 class="card-title">Add New Projects</h2>
            </header>
            <div class="card-body">
              <div class="row">
                <div class="col-12 col-md-3 mb-3">
                  <label class="form-label">Project Name</label>
                  <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                  @error('name')<div class="text-danger">{{ $message }}</div>@enderror
                </div>
                  
                <div class="col-12 col-md-2 mb-3">
                  <label class="form-label">Total Pieces</label>
                  <input type="number" name="total_pcs" class="form-control" required value="{{ old('total_pcs') }}">
                  @error('total_pcs')<div class="text-danger">{{ $message }}</div>@enderror
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
                  @error('status_id')<div class="text-danger">{{ $message }}</div>@enderror
                  </div>
                  <div class="col-12 col-md-4 mb-3">
                    <label class="form-label">Attachments</label>
                    <input type="file" name="attachments[]" class="form-control" multiple accept="image/*">
                    @if ($errors->has('attachments.*'))
                      @foreach ($errors->get('attachments.*') as $messages)
                        @foreach ($messages as $message)
                          <div class="text-danger">{{ $message }}</div>
                        @endforeach
                      @endforeach
                    @endif                            
                  </div>
                  <div class="col-12 col-md-5 mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
                    @error('description')<div class="text-danger">{{ $message }}</div>@enderror
                  </div>
                  <div id="preview-images" class="mt-2"></div>
              </div>
            </div>
        </section>

        <section class="card">
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
                  <th width="20%">Task</th>
                  <th>Description</th>
                  <th>Due Date</th>
                  <th>Due Time</th>
                  <th>Category</th>
                  <th>Status</th>
                  <th></th>
                </tr>
              </thead>
              <tbody id="ProjectTaskTable">
                <tr>
                  <td>
                    <input type="text" name="tasks[0][task_name]" class="form-control" placeholder="Task Name" required />
                    <input type="hidden" name="tasks[0][sort_order]" value="0" class="sort-order-field" />
                  </td>
                  <td>
                    <input type="text" name="tasks[0][description]" class="form-control" placeholder="Description" />
                  </td>
                  <td>
                    <input type="date" name="tasks[0][due_date]" class="form-control" />
                  </td>
                  <td>
                    <input type="time" name="tasks[0][due_time]" class="form-control" />
                  </td>
                  <td>
                    <select class="form-control select2-js" name="tasks[0][category_id]">
                      <option value="" selected disabled>Task Category</option>
                      @foreach ($taskCat as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                      @endforeach
                    </select>
                  </td>
                  <td>
                    <select class="form-control select2-js" name="tasks[0][status_id]">
                      <option value="" selected disabled>Task Status</option>
                      @foreach ($statuses as $status)
                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                      @endforeach
                    </select>
                  </td>
                  <td>
                    <button type="button" onclick="removeRow(this)" class="btn btn-danger"><i class="fas fa-times"></i></button>
                    <button type="button" class="btn btn-primary" onclick="addNewRow()"><i class="fa fa-plus"></i></button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <footer class="card-footer text-end mt-2">
            <a class="btn btn-danger" href="{{ route('projects.index') }}">Discard</a>
            <button type="submit" class="btn btn-primary">Create</button>
          </footer>
        </section>
      </form>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

  <script>
    function previewImages(event) {
        const preview = document.getElementById('preview-images');
        preview.innerHTML = '';
        Array.from(event.target.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'me-2 mb-2';
                img.style.maxHeight = '100px';
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    }

    var index=2;

    function addNewRow() {
    const lastRow = document.querySelector('#ProjectTaskTable tr:last-child');
    const lastTaskName = lastRow.querySelector('input[name*="[task_name]"]').value;

    if (!lastTaskName.trim()) {
      alert("Please fill in the previous task before adding a new one.");
      return;
    }

    const table = document.getElementById('ProjectTaskTable');
    const row = table.insertRow();

    row.innerHTML = `
      <td>
        <input type="text" name="tasks[${index}][task_name]" class="form-control" placeholder="Task Name" required />
        <input type="hidden" name="tasks[${index}][sort_order]" value="${index}" class="sort-order-field" />
      </td>
      <td>
        <input type="text" name="tasks[${index}][description]" class="form-control" placeholder="Description" />
      </td>
      <td>
        <input type="date" name="tasks[${index}][due_date]" class="form-control" />
      </td>
      <td>
        <input type="time" name="tasks[${index}][due_time]" class="form-control" />
      </td>
      <td>
        <select class="form-control select2-js" name="tasks[${index}][category_id]">
          <option value="" selected disabled>Select Category</option>
          @foreach ($taskCat as $cat)
            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
          @endforeach
        </select>
      </td>
      <td>
        <select class="form-control select2-js" name="tasks[${index}][status_id]">
          <option value="" selected disabled>Select Status</option>
          @foreach ($statuses as $status)
            <option value="{{ $status->id }}">{{ $status->name }}</option>
          @endforeach
        </select>
      </td>
      <td>
        <button type="button" onclick="removeRow(this)" class="btn btn-danger"><i class="fas fa-times"></i></button>
        <button type="button" class="btn btn-primary" onclick="addNewRow()"><i class="fa fa-plus"></i></button>
      </td>
    `;

    $('.select2-js').select2(); // re-init Select2
    index++;
  }

  function removeRow(button) {
    const tableRows = document.querySelectorAll('#ProjectTaskTable tr');
    if (tableRows.length > 1) {
      button.closest('tr').remove();
      updateSortOrder(); // update sort orders after removal
    }
  }

  function updateSortOrder() {
    const rows = document.querySelectorAll('#ProjectTaskTable tr');
    rows.forEach((row, i) => {
      const hidden = row.querySelector('input.sort-order-field');
      if (hidden) hidden.value = i;
    });
  }

  // Initialize Sortable
  new Sortable(document.getElementById('ProjectTaskTable'), {
    animation: 150,
    onEnd: updateSortOrder,
  });

  </script>

@endsection