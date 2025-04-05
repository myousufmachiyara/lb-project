@extends('layouts.app')

@section('title', 'All Projects')

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
                    <h2 class="card-title">All Projects</h2>
                    <div>
                        <a href="{{ route('projects.create') }}" class="btn btn-primary"> <i class="fas fa-plus"></i> Add Project</a>
                    </div>
                </header>
                <div class="card-body">
                    <div class="modal-wrapper table-scroll">
                        <table class="table table-bordered table-striped mb-0" id="cust-datatable-default">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($projects as $project)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @php
                                            $imageAttachment = $project->attachments->firstWhere(function ($att) {
                                                $ext = strtolower(pathinfo($att->att_path, PATHINFO_EXTENSION));
                                                return in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
                                            });
                                        @endphp

                                        @if ($imageAttachment)
                                            <img src="{{ asset('public/storage/' . $imageAttachment->att_path) }}" alt="Project Image" width="60" height="60" style="object-fit: cover; border-radius: 6px;">
                                        @else
                                            <span class="text-muted">No Image</span>
                                        @endif
                                    </td>
                                    <td>{{ $project->name }}</td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $project->status->color ?? '#ccc' }}">
                                            {{ $project->status->name ?? 'No Status' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('projects.edit', $project->id) }}" class="text-primary"><i class="fa fa-edit"></i></a>
                                        <form action="{{ route('projects.destroy', $project->id) }}" method="POST" style="display:inline;">
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
