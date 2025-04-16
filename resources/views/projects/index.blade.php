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
                    <div>
                        <div class="col-md-5" style="display:flex;">
                            <select class="form-control" style="margin-right:10px" id="columnSelect">
                                <option selected disabled>Search by</option>
                                <option value="2">by Name</option>
                                <option value="4">by Status</option>
                            </select>
                            <input type="text" class="form-control" id="columnSearch" placeholder="Search By Column"/>
                        </div>
                    </div>
                    <div class="modal-wrapper table-scroll">
                        <table class="table table-bordered table-striped mb-0" id="cust-datatable-default">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Pieces</th>
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
                                        <a href="{{ asset($imageAttachment->att_path) }}" data-plugin-lightbox="" data-plugin-options="{ &quot;type&quot;:&quot;image&quot; }" title="{{ $project->name }}">
                                            <img class="img-fluid" src="{{ asset($imageAttachment->att_path) }}" alt="Project Image" width="60" height="60" style="object-fit: cover; border-radius: 6px;">
                                        </a>
                                        @else
                                            <span class="text-muted">No Image</span>
                                        @endif
                                    </td>
                                    <td>{{ $project->name }}</td>
                                    <td>{{ $project->total_pcs }}</td>
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
    <script>
        $(document).ready(function(){
            var table = $('#cust-datatable-default').DataTable(
                {
                    "order": [[0, "desc"]],
                    "pageLength": 25,  // Show all rows
                }
            );

            $('#columnSelect').on('change', function () {
                // Clear the previous search
                table.search('').columns().search('').draw(); // Reset global and column-specific filters
            });
            $('#columnSearch').on('keyup change', function () {
                var columnIndex = $('#columnSelect').val(); // Get selected column index
                table.column(columnIndex).search(this.value).draw(); // Apply search and redraw
            });
        });
    </script>
@endsection
