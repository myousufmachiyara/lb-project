@extends('layouts.app')

@section('title', 'Sales | All Quotation')

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
                <header class="card-header" style="display: flex;justify-content: space-between;">
                    <h2 class="card-title">All Quotations</h2>
                    <div>
                        <a href="{{ route('quotations.create') }}" class="btn btn-primary"> <i class="fas fa-plus"></i> New Quotation </a>
                    </div>
                </header>
                <div class="card-body">
                    <div class="modal-wrapper table-scroll">
                        <table class="table table-bordered table-striped mb-0" id="cust-datatable-default">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Customer Name</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quotations as $quotation)
                                    <tr>
                                        <td>{{ $quotation->id }}</td>
                                        <td>{{ $quotation->customer_name }}</td>
                                        <td>{{ $quotation->date }}</td>
                                        <td>
                                            <a href="{{ route('quotations.print', $quotation->id) }}" target="_blank" class="text-success"><i class="fa fa-print"></i></a>
                                            <a href="{{ route('quotations.edit', $quotation->id) }}" class="text-primary"><i class="fa fa-edit"></i></a>
                                            <form action="{{ route('quotations.destroy', $quotation->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-danger bg-transparent" style="border:none" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div> 
@endsection
