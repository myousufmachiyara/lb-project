@extends('layouts.app') {{-- Or your admin layout --}}

@section('content')
<div class="container">
    <h2 class="mb-4">Modules List</h2>

    <a href="{{ route('modules.create') }}" class="btn btn-primary mb-3">Add New Module</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Module Name</th>
                <th>Shortcode</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($modules as $index => $module)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $module->name }}</td>
                    <td>{{ $module->shortcode }}</td>
                    <td>
                        @if ($module->status)
                            <span class="badge bg-success">Enabled</span>
                        @else
                            <span class="badge bg-danger">Disabled</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('modules.edit', $module->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('modules.destroy', $module->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">No modules found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
