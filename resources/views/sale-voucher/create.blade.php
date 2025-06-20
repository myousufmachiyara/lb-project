@extends('layouts.app')

@section('title', 'Sales | New Voucher')

@section('content')
<div class="row">
  <div class="col">
    <form action="{{ route('quotations.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <section class="card">
            @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @elseif (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <header class="card-header" style="display: flex; justify-content: space-between;">
            <h2 class="card-title">New Voucher</h2>
            </header>

            <div class="card-body">
            <div class="row">
                <div class="col-md-2 mb-3">
                <label>Customer Name</label>
                <input type="text" name="customer_name" class="form-control" required value="{{ old('customer_name') }}">
                @error('customer_name')<div class="text-danger">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2 mb-3">
                <label>Date</label>
                <input type="date" name="date" class="form-control" value="{{ old('date', now()->format('Y-m-d')) }}" required>
                @error('date')<div class="text-danger">{{ $message }}</div>@enderror
                </div>
            </div>
            </div>
        </section>

        <section class="card mt-3">
            <header class="card-header" style="display: flex; justify-content: space-between;">
            <h2 class="card-title">Voucher Details</h2>
            </header>

            <div class="card-body" style="max-height: 500px; overflow-y: auto">
            <table class="table table-bordered" id="quotationDetailsTable">
                <thead>
                <tr>
                    <th width="15%">Image</th>
                    <th width="15%">Service</th>
                    <th width="8%">Charges(%)</th>
                    <th>Description</th>
                    <th width="6%">Qty</th>
                    <th width="8%">Unit</th>
                    <th width="8%">Cost</th>
                    <th width="8%">Total</th>
                    <th width="6%">Action</th>
                </tr>
                </thead>
                <tbody id="quotationDetailsBody">
                @php $i = 0; @endphp
                <tr>
                    <td><input type="file" name="details[{{ $i }}][image]" class="form-control"></td>
                    <td><select class="form-control select2-js" name="details[{{ $i }}][service_id]">
                        <option value="" selected disabled>Select Service</option>
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                        @endforeach
                    </select></td>
                    <td><input type="number" step="0.01" name="details[{{ $i }}][service_charges_per_pc]" class="form-control" disabled></td>
                    <td><input type="text" name="details[{{ $i }}][description]" class="form-control"></td>
                    <td><input type="number" name="details[{{ $i }}][quantity]" class="form-control" required></td>
                    <td><input type="text" name="details[{{ $i }}][unit]" class="form-control" required></td>
                    <td><input type="number" step="0.01" name="details[{{ $i }}][cost]" class="form-control" required></td>
                    <td><input type="number" class="form-control" disabled></td>
                    <td>
                    <button type="button" onclick="removeRow(this)" class="text-danger bg-transparent" style="border:none"><i class="fas fa-times"></i></button>
                    <button type="button" class="text-primary bg-transparent" style="border:none" onclick="addNewRow()"><i class="fa fa-plus"></i></button>
                    </td>
                </tr>
                </tbody>
            </table>
            </div>

            <footer class="card-footer text-end">
            <a href="{{ route('quotations.index') }}" class="btn btn-danger">Discard</a>
            <button type="submit" class="btn btn-primary">Create</button>
            </footer>
        </section>
    </form>
  </div>
</div>

<script>
  let index = 1;

  function addNewRow() {
    const tbody = document.getElementById('quotationDetailsBody');
    const row = document.createElement('tr');

    row.innerHTML = `
      <td><input type="file" name="details[${index}][image]" class="form-control"></td>
      <td>
        <select class="form-control select2-js" name="details[${index}][service_id]" required>
          <option value="" selected disabled>Select Category</option>
          @foreach ($services as $service)
            <option value="{{ $service->id }}">{{ $service->name }}</option>
          @endforeach
        </select>
      </td>
      <td><input type="number" step="0.01" name="details[${index}][service_charges_per_pc]" class="form-control" disabled></td>
      <td><input type="text" name="details[${index}][description]" class="form-control"></td>
      <td><input type="number" name="details[${index}][quantity]" class="form-control" required></td>
      <td><input type="text" name="details[${index}][unit]" class="form-control" required></td>
      <td><input type="number" step="0.01" name="details[${index}][cost]" class="form-control" required></td>
      <td><input type="number" class="form-control" disabled></td>
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
