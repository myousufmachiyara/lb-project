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
                                    @php
                                        $pcsIn = $project->pcsInOut->where('type', 'in')->sum('pcs');
                                        $pcsOut = $project->pcsInOut->where('type', 'out')->sum('pcs');
                                        $totalOrdered = $project->total_pcs + $pcsIn;
                                        $remaining = $totalOrdered - $pcsOut;
                                    @endphp
                                    <td>{{ $totalOrdered }}</td>
                                    <td>{{ $pcsOut }}</td>
                                    <td>{{ $remaining }}</td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $project->status->color ?? '#ccc' }}">
                                            {{ $project->status->name ?? 'No Status' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a class="text-success showPcsBtn" data-name="{{ $project->name }}" data-id="{{ $project->id }}"><i class="fa fa-eye"></i></a>
                                        <a class="text-dark updatePcsBtn" data-name="{{ $project->name }}" data-id="{{ $project->id }}"><i class="fa fa-retweet"></i></a>
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
            <div id="updatePcsModal" class="modal-block modal-block-primary mfp-hide">
                <section class="card">
                    <form method="POST" action="" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                        @csrf
                        <header class="card-header">
                            <h2 class="card-title">Pieces Update</h2>
                        </header>
                        <div class="card-body">
                            <div class="row mb-3 form-group">
                                <div class="col-12 col-md-6 mb-2">
                                    <label>Project Name</label>
                                    <input type="text" class="form-control" id="update_project_name" disabled>
                                    <input type="hidden" class="form-control" id="update_project_id" name="project_id">
                                </div>
                                <div class="col-12 col-md-6 mb-2">
                                    <label>Date<span style="color: red;"><strong>*</strong></span></label>
                                    <input type="date" class="form-control" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-12 col-md-6 mb-2">
                                    <label>Type<span style="color: red;"><strong>*</strong></span></label>
                                    <select name="type" class="form-select" required>
                                        <option value="" disabled selected>-- Select Type --</option>
                                        <option value="in">Add (In)</option>
                                        <option value="out">Subtract (Out)</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6 mb-2">
                                    <label>Pieces In/Out<span style="color: red;"><strong>*</strong></span></label>
                                    <input type="number" class="form-control" placeholder="Total Pieces" name="pcs" required>
                                </div>
                                <div class="col-12">
                                    <label>Remarks</label>
                                    <textarea rows="4" cols="50" class="form-control" placeholder="Remarks" name="remarks"> </textarea>                            
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
            <div id="showPcsModal" class="modal-block modal-block-primary mfp-hide">
                <section class="card">
                    <header class="card-header">
                        <h2 class="card-title">Project Pcs In/Out Record</h2>
                    </header>
                    <div class="card-body">
                        <table class="table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Pieces</th>
                                    <th>Remarks</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="pcsTableBody">

                            </tbody>
                        </table>
                        <footer class="card-footer">
                            <div class="row">
                                <div class="col-md-12 text-end">
                                    <button class="btn btn-default modal-dismiss">Close</button>
                                </div>
                            </div>
                        </footer>
                    </div>
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

        

        $('.updatePcsBtn').on('click', function () {
            const projectId = $(this).data('id');
            const projectName = $(this).data('name');
            const formAction = `/project-pcs-update/${projectId}`;
            $('#update_project_id').val(projectId);
            $('#update_project_name').val(projectName);
            $('#updatePcsModal form').attr('action', formAction);

            // Open modal
            $.magnificPopup.open({
              items: {
                src: '#updatePcsModal'
              },
              type: 'inline'
            });        
        });

        $('.showPcsBtn').on('click', function () {
            const projectId = $(this).data('id');
            const tableBody = $('#pcsTableBody');

            // Clear previous data
            tableBody.empty();

            // Open modal
            $.magnificPopup.open({
                items: {
                    src: '#showPcsModal'
                },
                type: 'inline'
            });

            // AJAX call to get pcs data
            $.ajax({
                url: `/project-pcs-show/${projectId}`,
                type: 'GET',
                success: function (data) {
                    if (data.length === 0) {
                        tableBody.append(`<tr><td colspan="6" class="text-center">No records found</td></tr>`);
                    } else {
                        data.forEach((item, index) => {
                            tableBody.append(`
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${item.date}</td>
                                    <td>${item.type}</td>
                                    <td>${item.pcs}</td>
                                    <td>${item.remarks}</td>
                                    <td>
                                        <a class="deletePcsBtn text-danger" data-id="${item.id}">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            `);
                        });
                    }
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                }
            });
        });

        $(document).on('click', '.deletePcsBtn', function () {
            const btn = $(this);
            const id = btn.data('id');

            if (confirm('Are you sure you want to delete this record?')) {
                $.ajax({
                    url: `/project-pcs-delete/${id}`,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content') // important for Laravel
                    },
                    success: function (response) {
                        if (response.success) {
                            // Remove the row from the table
                            btn.closest('tr').remove();
                        }
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                    }
                });
            }
        });
    </script>
@endsection
