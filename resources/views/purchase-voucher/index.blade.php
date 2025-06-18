@extends('layouts.app')

@section('title', 'Purchases | All Vouchers')

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

            <header class="card-header d-flex justify-content-between">
                <h2 class="card-title">All Purchase Vouchers</h2>
                <a href="{{ route('purchase-vouchers.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Voucher
                </a>
            </header>

            <div class="card-body">
                <div class="modal-wrapper table-scroll">
                    <table class="table table-bordered table-striped mb-0" id="cust-datatable-default">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Voucher ID</th>
                                <th>Vendor Name</th>
                                <th>Date</th>
                                <th>Total Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vouchers as $index => $voucher)
                                @php
                                    $total = $voucher->details->sum(function($detail) {
                                        return $detail->qty * $detail->rate;
                                    });
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $voucher->voucher_id }}</td>
                                    <td>{{ $voucher->coa->name ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($voucher->date)->format('d M, Y') }}</td>
                                    <td>{{ number_format($total, 2) }}</td>
                                    <td>
                                        <a href="{{ route('purchase-vouchers.edit', $voucher->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('purchase-vouchers.destroy', $voucher->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                        {{-- Optional: Print Button --}}
                                        <a href="{{ route('pv.print', $voucher->id) }}" class="btn btn-sm btn-info">Print</a>
                                    </td>
                                </tr>
                            @endforeach

                            @if ($vouchers->isEmpty())
                                <tr>
                                    <td colspan="6" class="text-center">No vouchers found.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
