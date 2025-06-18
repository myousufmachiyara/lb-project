@extends('layouts.app')

@section('title', 'Project Costing')

@section('content')
<div class="row">
  <div class="col">
    <form action="{{ route('projects.costing.store', $project->id) }}" method="POST">
      @csrf
      <section class="card">
        <header class="card-header d-flex justify-content-between">
          <h2 class="card-title">Project Costing for: {{ $project->name }}</h2>
        </header>

        <div class="card-body">
          @if ($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <div class="row">
            <div class="col-md-4 mb-3">
              <label for="date">Date</label>
              <input type="date" name="date" class="form-control" value="{{ old('date', now()->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-8 mb-3">
              <label for="remarks">Remarks</label>
              <input type="text" name="remarks" class="form-control" value="{{ old('remarks') }}">
            </div>
          </div>

          <div class="table-responsive mt-4">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Image</th>
                  <th>Service</th>
                  <th>Description</th>
                  <th>Qty</th>
                  <th>Rate</th>
                  <th>Service %</th>
                  <th>Total Rate</th>
                  <th>Total Amount</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $grandTotal = 0;
                @endphp
                @foreach ($purchaseDetails as $detail)
                  @php
                    $service = $services->firstWhere('name', $detail->service);
                    $percentage = $service->charges_per_pc ?? 0;
                    $totalRate = $detail->rate + ($detail->rate * $percentage / 100);
                    $totalAmount = $totalRate * $detail->qty;
                    $grandTotal += $totalAmount;
                  @endphp
                  <tr>
                    <td>
                      @if ($detail->image)
                        <img src="{{ asset('storage/' . $detail->image) }}" alt="img" width="50">
                      @else
                        N/A
                      @endif
                    </td>
                    <td>{{ $detail->service }}</td>
                    <td>{{ $detail->description }}</td>
                    <td>{{ $detail->qty }}</td>
                    <td>{{ number_format($detail->rate, 2) }}</td>
                    <td>{{ $percentage }}%</td>
                    <td>{{ number_format($totalRate, 2) }}</td>
                    <td>{{ number_format($totalAmount, 2) }}</td>
                  </tr>
                @endforeach
              </tbody>
              <tfoot>
                <tr>
                  <th colspan="7" class="text-end">Grand Total</th>
                  <th>{{ number_format($grandTotal, 2) }}</th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        <footer class="card-footer text-end">
          <a href="{{ route('projects.index') }}" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary">Save Costing</button>
        </footer>
      </section>
    </form>
  </div>
</div>
@endsection
