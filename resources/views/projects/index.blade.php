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
                                <option value="6">by Status</option>
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
                                    <th>Ordered</th>
                                    <th>Delivered</th>
                                    <th>Remaining</th>
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
                                    <td>-</td>
                                    <td>-</td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $project->status->color ?? '#ccc' }}">
                                            {{ $project->status->name ?? 'No Status' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a class="text-success showCountBtn" data-name="{{ $project->name }}" data-id="{{ $project->id }}"><i class="fa fa-eye"></i></a>
                                        <a class="text-dark updateCountBtn" data-name="{{ $project->name }}" data-id="{{ $project->id }}"><i class="fa fa-retweet"></i></a>
                                        <a href="{{ route('projects.edit', $project->id) }}" class="text-primary"><i class="fa fa-edit"></i></a>
                                        <form action="{{ route('projects.destroy', $project->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <a class="text-danger bg-transparent" style="border:none" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></a>
                                        </form>                                       
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>   
                    </div> 
                </div>
            </section>
            <div id="updateCountModal" class="modal-block modal-block-primary mfp-hide">
                <section class="card">
                    <form method="post" action="" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                        @csrf
                        <header class="card-header">
                            <h2 class="card-title">Pieces Transaction</h2>
                        </header>
                        <div class="card-body">
                            <div class="row mb-3 form-group">
                                <div class="col-12 col-md-6 mb-2">
                                    <label>Project Name</label>
                                    <input type="text" class="form-control" id="count_project_name" placeholder="Name" name="name" disabled>
                                    <input type="hidden" class="form-control" id="count_project_id" required disabled>
                                    <input type="hidden" name="_method" value="PUT">
                                </div>
                                <div class="col-12 col-md-6 mb-2">
                                    <label>Date<span style="color: red;"><strong>*</strong></span></label>
                                    <input type="date" class="form-control" name="trans_date" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-12 col-md-6 mb-2">
                                    <label>Type<span style="color: red;"><strong>*</strong></span></label>
                                    <select name="transaction_type" class="form-select" required>
                                        <option value="" disabled selected>-- Select Type --</option>
                                        <option value="in">Add (In)</option>
                                        <option value="out">Subtract (Out)</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6 mb-2">
                                    <label>Pieces Count<span style="color: red;"><strong>*</strong></span></label>
                                    <input type="number" class="form-control" placeholder="Total Pieces" name="pcs_count" required>
                                </div>
                                <div class="col-12">
                                    <label>Remarks</label>
                                    <textarea rows="4" cols="50" class="form-control" placeholder="Remarks" name="remarks"> </textarea>                            </div>
                                </div>
                            </div>
                            <footer class="card-footer">
                                <div class="row">
                                    <div class="col-md-12 text-end">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                        <button class="btn btn-default modal-dismiss">Cancel</button>
                                    </div>
                                </div>
                            </footer>
                        </div>
                    </form>
                </section>
            </div>
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

            table.on('draw', function () {
                if (typeof $.fn.themePluginLightbox !== 'undefined') {
                    $('[data-plugin-lightbox]').each(function () {
                        var $this = $(this),
                            opts;

                        var pluginOptions = $this.data('plugin-options');
                        if (pluginOptions) {
                            opts = pluginOptions;
                        }

                        $this.themePluginLightbox(opts);
                    });
                }
            });

            $('#columnSelect').on('change', function () {
                // Clear the previous search
                table.search('').columns().search('').draw(); // Reset global and column-specific filters
            });
            $('#columnSearch').on('keyup change', function () {
                var columnIndex = $('#columnSelect').val(); // Get selected column index
                table.column(columnIndex).search(this.value).draw(); // Apply search and redraw
            });
        });

        $('.updateCountBtn').on('click', function () {
            const projectId = $(this).data('id');
            const projectName = $(this).data('name');
            const formAction = `/project-count/${projectId}`;
            $('#count_project_id').val(projectId);
            $('#count_project_name').val(projectName);
            $('#updateCountModal form').attr('action', formAction);

            // Open modal
            $.magnificPopup.open({
              items: {
                src: '#updateCountModal'
              },
              type: 'inline'
            });        
        });
    </script>
@endsection
