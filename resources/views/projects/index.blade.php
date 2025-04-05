@extends('layouts.app')

@section('title', 'All Projects')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Projects</h2>
        <a href="{{ route('projects.create') }}" class="btn btn-primary">Add Project</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Name</th>
                <th>Status</th>
                <th>Dates</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($projects as $project)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $project->name }}</td>
                <td>
                    <span class="badge" style="background-color: {{ $project->status->color }};">
                        {{ $project->status->name }}
                    </span>
                </td>
                <td>{{ $project->start_date }} - {{ $project->end_date }}</td>
                <td>
                    <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
