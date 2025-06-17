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
                <header class="card-header" style="display: flex;justify-content: space-between;">
                    <h2 class="card-title">All Purchase Vouchers</h2>
                    <div>
                        <a href="{{ route('purchase-vouchers.create') }}" class="btn btn-primary"> <i class="fas fa-plus"></i> New Voucher </a>
                    </div>
                </header>
                <div class="card-body">
                    <div class="modal-wrapper table-scroll">
                        <table class="table table-bordered table-striped mb-0" id="cust-datatable-default">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div> 
@endsection
