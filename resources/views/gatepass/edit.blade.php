@extends('layouts.app')

@section('title', 'Gatepass | Edit Gatepass')

@section('content')
<div class="row">
  <div class="col">
    <form action="{{ route('gatepass.update', $gatepass->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <section class="card">
          @if ($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <header class="card-header d-flex justify-content-between">
            <h2 class="card-title">Edit Gatepass</h2>
          </header>

          <div class="card-body">
            <div class="row">
              <div class="col-md-4 mb-3">
                <label>Vendor / COA</label>
                <select name="coa_id" class="form-control" required>
                  <option value="" selected disabled>Select Vendor</option>
                  @foreach ($coas as $coa)
                    <option value="{{ $coa->id }}" {{ $gatepass->coa_id == $coa->id ? 'selected' : '' }}>
                      {{ $coa->name }}
                    </option>
                  @endforeach
                </select>
                @error('coa_id')<div class="text-danger">{{ $message }}</div>@enderror
              </div>

              <div class="col-md-4 mb-3">
                <label>Date</label>
                <input type="date" name="date" class="form-control" value="{{ old('date', $gatepass->date) }}" required>
                @error('date')<div class="text-danger">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>
        </section>

        <section class="card mt-3">
          <header class="card-header d-flex justify-content-between">
            <h2 class="card-title">Gatepass Details</h2>
          </header>

          <div class="card-body" style="max-height: 500px; overflow-y: auto">
            <table class="table table-bordered" id="gatepassDetailsTable">
              <thead>
                <tr>
                  <th width="12%">Image</th>
                  <th width="15%">Project</th>
                  <th width="15%">Service</th>
                  <th>Description</th>
                  <th width="6%">Qty</th>
                  <th width="8%">Unit</th>
                  <th width="8%">Rate</th>
                  <th width="8%">Action</th>
                </tr>
              </thead>
              <tbody id="gatepassDetailsBody">
                @foreach ($gatepass->details as $index => $detail)
                  <tr>
                    <td>
                      @if ($detail->image)
                        <img src="{{ asset('storage/' . $detail->image) }}" width="40" class="mb-1">
                      @endif
                      <input type="file" name="details[{{ $index }}][image]" class="form-control">
                    </td>
                    <td>
                      <select name="details[{{ $index }}][project_id]" class="form-control" required>
                        <option value="" disabled>Select Project</option>
                        @foreach ($projects as $project)
                          <option value="{{ $project->id }}" {{ $detail->project_id == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                          </option>
                        @endforeach
                      </select>
                    </td>
                    <td>
                      <select name="details[{{ $index }}][service_id]" class="form-control" required>
                        <option value="" disabled>Select Service</option>
                        @foreach ($services as $service)
                          <option value="{{ $service->id }}" {{ $detail->service_id == $service->id ? 'selected' : '' }}>
                              {{ $service->name }}
                          </option>
                        @endforeach
                      </select>
                    </td>
                    <td><input type="text" name="details[{{ $index }}][description]" class="form-control" value="{{ $detail->description }}"></td>
                    <td><input type="number" name="details[{{ $index }}][qty]" class="form-control" value="{{ $detail->qty }}" required></td>
                    <td><input type="text" name="details[{{ $index }}][unit]" class="form-control" value="{{ $detail->unit }}" required></td>
                    <td><input type="number" step="0.01" name="details[{{ $index }}][rate]" class="form-control" value="{{ $detail->rate }}" required></td>
                    <td>
                      <button type="button" onclick="removeRow(this)" class="btn btn-danger"><i class="fas fa-times"></i></button>
                      <button type="button" onclick="addNewRow()" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <footer class="card-footer text-end">
            <a href="{{ route('gatepass.index') }}" class="btn btn-danger">Cancel</a>
            <button type="submit" class="btn btn-success">Update</button>
          </footer>
        </section>
    </form>
  </div>
</div>

<script>
  let index = {{ $gatepass->details->count() }};

  function addNewRow() {
    const tbody = document.getElementById('gatepassDetailsBody');
    const row = document.createElement('tr');

    row.innerHTML = `
      <td><input type="file" name="details[\${index}][image]" class="form-control"></td>
      <td>
        <select name="details[\${index}][project_id]" class="form-control" required>
          <option value="" disabled selected>Select Project</option>
          @foreach ($projects as $project)
            <option value="{{ $project->id }}">{{ $project->name }}</option>
          @endforeach
        </select>
      </td>
      <td>
        <select name="details[\${index}][service]" class="form-control" required>
          <option value="" disabled selected>Select Service</option>
          @foreach ($services as $service)
            <option value="{{ $service->name }}">{{ $service->name }}</option>
          @endforeach
        </select>
      </td>
      <td><input type="text" name="details[\${index}][description]" class="form-control"></td>
      <td><input type="number" name="details[\${index}][qty]" class="form-control" required></td>
      <td><input type="text" name="details[\${index}][unit]" class="form-control" required></td>
      <td><input type="number" step="0.01" name="details[\${index}][rate]" class="form-control" required></td>
      <td>
        <button type="button" onclick="removeRow(this)" class="btn btn-danger"><i class="fas fa-times"></i></button>
        <button type="button" onclick="addNewRow()" class="btn btn-primary"><i class="fa fa-plus"></i></button>
      </td>
    `;

    tbody.appendChild(row);
    index++;
  }

  function removeRow(btn) {
    btn.closest('tr').remove();
  }
</script>
@endsection
