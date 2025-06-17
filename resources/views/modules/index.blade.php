@extends('layouts.app')

@section('title', 'Modules | All Modules')

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
                    <h2 class="card-title">All Modules</h2>
                    <div>
                        <button type="button" class="modal-with-form btn btn-primary" href="#addModal"> <i class="fas fa-plus"></i> Add New</button>
                    </div>
                </header>
                <div class="card-body">
                    <div class="modal-wrapper table-scroll">
                        <table class="table table-bordered table-striped mb-0" id="cust-datatable-default">
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
                                            <a class="text-primary editBtn" data-id="{{ $module->id }}"><i class="fa fa-edit"></i></a>
                                            <!-- <form action="{{ route('modules.destroy', $module->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-danger bg-transparent" style="border:none" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></button>
                                            </form> -->
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center">No modules found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <div id="addModal" class="modal-block modal-block-primary mfp-hide">
                <section class="card">
                    <form method="POST" action="{{ route('modules.store') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                        @csrf
                        <header class="card-header">
                            <h2 class="card-title">Add New Module</h2>
                        </header>
                        <div class="card-body">
                            <div class="row form-group pb-2">
                                <div class="col-6 mt-2">
                                    <label>Module Name <span class="text-danger"><strong>*</strong></span></label>
                                    <input type="text" name="name" class="form-control" required>
                                    @error('name')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-6 mt-2">
                                    <label>Shortcode <span class="text-danger"><strong>*</strong></span></label>
                                    <input type="text" name="shortcode" class="form-control" required>
                                    @error('shortcode')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-6 mt-2">
                                    <label>Status <span class="text-danger"><strong>*</strong></span></label>
                                    <select name="status" class="form-control" required>
                                        <option value="1">Enabled</option>
                                        <option value="0">Disabled</option>
                                    </select>
                                    @error('status')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                        <footer class="card-footer">
                            <div class="row">
                                <div class="col-md-12 text-end">
                                    <button type="submit" class="btn btn-success">Create</button>
                                    <button class="btn btn-default modal-dismiss">Cancel</button>
                                </div>
                            </div>
                        </footer>
                    </form>
                </section>
            </div>

            <div id="editModal" class="modal-block modal-block-primary mfp-hide">
                <section class="card">
                    <form method="POST" action="" id="editModuleForm" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                        @csrf
                        @method('PUT')
                        <header class="card-header">
                            <h2 class="card-title">Edit Module</h2>
                        </header>
                        <div class="card-body">
                            <input type="hidden" name="id" id="edit_module_id">

                            <div class="row form-group pb-2">
                                <div class="col-6 mt-2">
                                    <label>Module Name <span class="text-danger"><strong>*</strong></span></label>
                                    <input type="text" name="name" class="form-control" id="edit_module_name" required>
                                    @error('name')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-6 mt-2">
                                    <label>Shortcode <span class="text-danger"><strong>*</strong></span></label>
                                    <input type="text" name="shortcode" class="form-control" id="edit_module_shortcode" required>
                                    @error('shortcode')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-6 mt-2">
                                    <label>Status <span class="text-danger"><strong>*</strong></span></label>
                                    <select name="status" class="form-control" id="edit_module_status" required>
                                        <option value="1">Enabled</option>
                                        <option value="0">Disabled</option>
                                    </select>
                                    @error('status')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                        <footer class="card-footer">
                            <div class="row">
                                <div class="col-md-12 text-end">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <button class="btn btn-default modal-dismiss">Cancel</button>
                                </div>
                            </div>
                        </footer>
                    </form>
                </section>
            </div>
        </div>
    </div>  
    <script>
        $(document).ready(function () {
            $('.editBtn').on('click', function () {
                const moduleId = $(this).data('id');

                $.ajax({
                    url: `/modules/${moduleId}/json`, // create this route to return module as JSON
                    method: 'GET',
                    success: function (data) {
                        // Populate modal fields
                        $('#edit_module_id').val(data.id);
                        $('#edit_module_name').val(data.name);
                        $('#edit_module_shortcode').val(data.shortcode);
                        $('#edit_module_status').val(data.status);

                        // Set form action dynamically
                        const formAction = `/modules/${data.id}`;
                        $('#editModuleForm').attr('action', formAction);

                        // Open modal
                        $.magnificPopup.open({
                            items: {
                                src: '#editModal'
                            },
                            type: 'inline'
                        });
                    },
                    error: function () {
                        alert('Failed to fetch module data.');
                    }
                });
            });
        });
    </script>
@endsection
