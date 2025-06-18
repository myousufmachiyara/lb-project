@extends('layouts.app')

@section('title', 'Quotation | Edit Quotation')

@section('content')
<div class="row">
  <div class="col">
    <form action="{{ route('quotations.update', $quotation->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <section class="card">
          @if ($errors->any())
            <div class="alert alert-danger">
              <ul>
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <header class="card-header d-flex justify-content-between">
            <h2 class="card-title">Edit Quotation</h2>
          </header>

          <div class="card-body">
            <div class="row">
              <div class="col-md-2 mb-3">
                <label>Customer Name</label>
                <input type="text" name="customer_name" class="form-control" required value="{{ old('customer_name', $quotation->customer_name) }}">
                @error('customer_name')<div class="text-danger">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-2 mb-3">
                <label>Date</label>
                <input type="date" name="date" class="form-control" value="{{ old('date', \Carbon\Carbon::parse($quotation->date)->format('Y-m-d')) }}" />
                @error('date')<div class="text-danger">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>
        </section>

        <section class="card mt-3">
          <header class="card-header d-flex justify-content-between">
            <h2 class="card-title">Quotation Details</h2>
          </header>

          <div class="card-body" style="max-height: 500px; overflow-y: auto">
            <table class="table table-bordered" id="quotationDetailsTable">
              <thead>
                <tr>
                  <th width="15%">Image</th>
                  <th width="15%">Service</th>
                  <th width="10%">Price (%)</th>
                  <th>Description</th>
                  <th width="8%">Qty</th>
                  <th width="8%">Unit</th>
                  <th width="10%">Rate</th>
                  <th width="10%">Total</th>
                  <th width="8%">Action</th>
                </tr>
              </thead>
              <tbody id="quotationDetailsBody">
                @foreach ($quotation->details as $index => $detail)
                  <tr>
                    <td>
                      @if ($detail->image)
                        <a href="{{ asset('storage/' . $detail->image) }}" target="_blank">View</a><br>
                      @endif
                      <input type="file" name="details[{{ $index }}][image]" class="form-control">
                    </td>
                    <td>
                      <select class="form-control select2-js" name="details[{{ $index }}][service_id]" required onchange="updatePrice(this)">
                        <option value="" selected disabled>Select Service</option>
                        @foreach ($services as $service)
                          <option value="{{ $service->id }}" data-price="{{ $service->price }}" {{ $detail->service_id == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                        @endforeach
                      </select>
                    </td>
                    <td><input type="number" step="0.01" name="details[{{ $index }}][service_charges_per_pc]" class="form-control price-input" readonly value="{{ $detail->service_charges_per_pc }}"></td>
                    <td><input type="text" name="details[{{ $index }}][description]" class="form-control" value="{{ $detail->description }}"></td>
                    <td><input type="number" name="details[{{ $index }}][quantity]" class="form-control quantity-input" required value="{{ $detail->quantity }}"></td>
                    <td><input type="text" name="details[{{ $index }}][unit]" class="form-control" required value="{{ $detail->unit }}"></td>
                    <td><input type="number" step="0.01" name="details[{{ $index }}][cost]" class="form-control cost-input" required value="{{ $detail->cost }}"></td>
                    <td><input type="number" class="form-control total-output" readonly></td>
                    <td>
                      <button type="button" class="btn btn-danger" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
                      <button type="button" class="btn btn-primary" onclick="addNewRow()"><i class="fa fa-plus"></i></button>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <footer class="card-footer text-end">
            <a href="{{ route('quotations.index') }}" class="btn btn-danger">Cancel</a>
            <button type="submit" class="btn btn-success">Update</button>
          </footer>
        </section>
    </form>
  </div>
</div>

<script>
  let index = {{ count($quotation->details) }};

  function addNewRow() {
    const tbody = document.getElementById('quotationDetailsBody');
    const row = document.createElement('tr');

    row.innerHTML = `
      <td><input type="file" name="details[${index}][image]" class="form-control"></td>
      <td>
        <select class="form-control select2-js" name="details[${index}][service_id]" required onchange="updatePrice(this)">
          <option value="" selected disabled>Select Service</option>
          @foreach ($services as $service)
            <option value="{{ $service->id }}" data-price="{{ $service->price }}">{{ $service->name }}</option>
          @endforeach
        </select>
      </td>
      <td><input type="number" step="0.01" name="details[${index}][service_charges_per_pc]" class="form-control price-input" readonly></td>
      <td><input type="text" name="details[${index}][description]" class="form-control"></td>
      <td><input type="number" name="details[${index}][quantity]" class="form-control quantity-input" required></td>
      <td><input type="text" name="details[${index}][unit]" class="form-control" required></td>
      <td><input type="number" step="0.01" name="details[${index}][cost]" class="form-control cost-input" required></td>
      <td><input type="number" class="form-control total-output" readonly></td>
      <td>
        <button type="button" class="btn btn-danger" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
        <button type="button" class="btn btn-primary" onclick="addNewRow()"><i class="fa fa-plus"></i></button>
      </td>
    `;
    tbody.appendChild(row);
    index++;
  }

  function removeRow(btn) {
    btn.closest('tr').remove();
  }

  document.addEventListener('change', function (e) {
    if (e.target.matches('select[name^="details"]')) {
      const row = e.target.closest('tr');
      const selected = e.target.selectedOptions[0];
      const price = selected?.dataset?.price || 0;
      row.querySelector('.price-input').value = price;
      calculateTotal(row);
    }
  });

  document.addEventListener('input', function (e) {
    if (
      e.target.matches('.quantity-input') ||
      e.target.matches('.cost-input') ||
      e.target.matches('.price-input')
    ) {
      const row = e.target.closest('tr');
      calculateTotal(row);
    }
  });

  function calculateTotal(row) {
    const qty = parseFloat(row.querySelector('.quantity-input')?.value) || 0;
    const rate = parseFloat(row.querySelector('.cost-input')?.value) || 0;
    const price = parseFloat(row.querySelector('.price-input')?.value) || 0;

    const cost = qty * rate;
    const priceAmount = (price / 100) * cost;
    const total = cost + priceAmount;

    row.querySelector('.total-output').value = total.toFixed(2);
  }

  // Initialize totals on page load
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('#quotationDetailsBody tr').forEach(calculateTotal);
  });
</script>
@endsection
