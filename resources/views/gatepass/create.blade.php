@extends('layouts.app')

@section('title', 'Gatepass | New Entry')

@section('content')
<div class="row">
    <div class="col">
        <form action="{{ route('gatepass.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
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
                    <h2 class="card-title">New Gatepass</h2>
                </header>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Vendor Name</label>
                            <select name="coa_id" class="form-control" required>
                                <option value="" disabled selected>Select Vendor</option>
                                @foreach ($coas as $coa)
                                    <option value="{{ $coa->id }}" {{ old('coa_id', $gatepass->coa_id ?? '') == $coa->id ? 'selected' : '' }}>
                                    {{ $coa->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Date</label>
                            <input type="date" name="date" class="form-control" value="{{ old('date', now()->format('Y-m-d')) }}" required>
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
                            @php $i = 0; @endphp
                            <tr>
                                <td><input type="file" name="details[{{ $i }}][image]" class="form-control"></td>
                                <td>
                                    <select name="details[{{ $i }}][project_id]" class="form-control" required>
                                        <option value="" selected disabled>Select Project</option>
                                        @foreach ($projects as $project)
                                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="details[{{ $i }}][service_id]" class="form-control" required>
                                        <option value="" selected disabled>Select Service</option>
                                        @foreach ($services as $service)
                                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="text" name="details[{{ $i }}][description]" class="form-control"></td>
                                <td><input type="number" name="details[{{ $i }}][qty]" class="form-control" required></td>
                                <td><input type="text" name="details[{{ $i }}][unit]" class="form-control" required></td>
                                <td><input type="number" step="0.01" name="details[{{ $i }}][rate]" class="form-control" required></td>
                                <td>
                                    <button type="button" onclick="removeRow(this)" class="btn btn-danger"><i class="fas fa-times"></i></button>
                                    <button type="button" onclick="addNewRow()" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <footer class="card-footer text-end">
                    <a href="{{ route('gatepass.index') }}" class="btn btn-danger">Discard</a>
                    <button type="submit" class="btn btn-primary">Create</button>
                </footer>
            </section>
        </form>
    </div>
</div>

<script>
    let index = 1;

    function addNewRow() {
        const tbody = document.getElementById('gatepassDetailsBody');
        const row = document.createElement('tr');

        row.innerHTML = `
            <td><input type="file" name="details[${index}][image]" class="form-control"></td>
            <td>
                <select name="details[${index}][project_id]" class="form-control" required>
                    <option value="" selected disabled>Select Project</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <select name="details[${index}][service_id]" class="form-control" required>
                    <option value="" selected disabled>Select Service</option>
                    @foreach ($services as $service)
                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="text" name="details[${index}][description]" class="form-control"></td>
            <td><input type="number" name="details[${index}][qty]" class="form-control" required></td>
            <td><input type="text" name="details[${index}][unit]" class="form-control" required></td>
            <td><input type="number" step="0.01" name="details[${index}][rate]" class="form-control" required></td>
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
