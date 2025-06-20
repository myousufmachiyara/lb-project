@extends('layouts.app')

@section('title', 'Gatepasses | All Records')

@section('content')
<div class="row">
    <div class="col">
        <section class="card">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @elseif (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <header class="card-header d-flex justify-content-between">
                <h2 class="card-title">All Gatepasses</h2>
                <a href="{{ route('gatepass.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Gatepass
                </a>
            </header>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0" id="datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Vendor Name</th>
                                <th>Date</th>
                                <th>Total Qty</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($gatepasses as $index => $gatepass)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $gatepass->coa->name ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($gatepass->date)->format('d M, Y') }}</td>
                                    <td>{{ $gatepass->details->sum('qty') }}</td>
                                    <td>
                                        <a href="{{ route('gatepass.edit', $gatepass->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('gatepass.destroy', $gatepass->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center">No gatepasses found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
