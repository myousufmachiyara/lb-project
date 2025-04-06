@extends('layouts.app')

@section('title', 'Projects | New Project')

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
                    <h2 class="card-title">Add New Projects</h2>
                </header>
                <div class="card-body">
                    <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-md-2 mb-3">
                                <label class="form-label">Project Name</label>
                                <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                            </div>
                            <div class="col-12 col-md-3 mb-3">
                                <label class="form-label">Customer</label>
                                <select name="acc_id" class="form-select" required>
                                    <option value="">-- Select Customer --</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}" {{ old('acc_id') == $account->id ? 'selected' : '' }}>
                                            {{ $account->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-1 mb-3">
                                <label class="form-label">Total Pieces</label>
                                <input type="number" name="total_pcs" class="form-control" required value="{{ old('total_pcs') }}">
                            </div>
                            <div class="col-12 col-md-2 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status_id" class="form-select" required>
                                    <option value="">-- Select Status --</option>
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->id }}" {{ old('status_id') == $status->id ? 'selected' : '' }}>
                                            {{ $status->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label class="form-label">Attachments</label>
                                <input type="file" name="attachments[]" class="form-control" required multiple>
                            </div>
                            <div class="col-12 col-md-5 mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                            </div>
                            
                            <footer class="card-footer text-end mt-2">
                                <a class="btn btn-danger" href="{{ route('projects.index') }}">Discard</a>
                                <button type="submit" class="btn btn-primary">Create</button>
                            </footer>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
@endsection