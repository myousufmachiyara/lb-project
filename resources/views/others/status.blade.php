@extends('layouts.app')

@section('title', 'Projects | Status')

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
            <h2 class="card-title">All Project Status</h2>
            <div>
                <button type="button" class="modal-with-form btn btn-primary" href="#addModal"> <i class="fas fa-plus"></i> Add New</button>
            </div>
        </header>
        <div class="card-body">
          <div class="modal-wrapper table-scroll">
            <table class="table table-bordered table-striped mb-0" id="cust-datatable-default">
              <thead>
                <tr>
                    <th>S.No</th>
                    <th>Name</th>
                    <th>Color</th>
                    <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($statuses as $item)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->name }}</td>
                    <td><span style="background-color: {{ $item->color }}; color: white;">{{ $item->color }}</span></td>
                    <td>
                        <a class="text-primary editStatusBtn" data-id="{{ $item->id }}"><i class="fa fa-edit"></i></a>
                        <form action="{{ route('status.destroy', $item->id) }}" method="POST" style="display:inline;">
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

      <!-- Add Project Status Modal -->
      <div id="addModal" class="modal-block modal-block-primary mfp-hide">
        <section class="card">
          <form method="post" action="{{ route('status.store') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
            @csrf
            <header class="card-header">
                <h2 class="card-title">New Project Status</h2>
            </header>
            <div class="card-body">
                <div class="form-group mt-2">
                    <label>Project Status Name<span style="color: red;"><strong>*</strong></span></label>
                    <input type="text" class="form-control" placeholder="Name" name="name" required>
                </div>
                <div class="form-group mb-3">
                    <label>Color<span style="color: red;"><strong>*</strong></span></label>
                    <input type="color" class="form-control" name="color" required>
                </div>
            </div>
            <footer class="card-footer">
                <div class="row">
                    <div class="col-md-12 text-end">
                        <button type="submit" class="btn btn-primary">Add</button>
                        <button class="btn btn-default modal-dismiss">Cancel</button>
                    </div>
                </div>
            </footer>
          </form>
        </section>
      </div>

      <!-- Update Project Status Modal -->
      <div id="updateModal" class="modal-block modal-block-primary mfp-hide">
        <section class="card">
          <form method="post" action="" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
            @csrf
            <header class="card-header">
                <h2 class="card-title">Update Project Status</h2>
            </header>
            <div class="card-body">
                <div class="form-group">
                    <input type="number" class="form-control" id="update_status_id" required disabled>
                </div>
                <div class="form-group">
                    <label>Project Status Name<span style="color: red;"><strong>*</strong></span></label>
                    <input type="text" class="form-control" id="update_status_name" placeholder="Name" name="name" required>
                    <input type="hidden" class="form-control" id="status_id" name="status_id" required>
                </div>
                <div class="form-group mb-3">
                    <label>Color<span style="color: red;"><strong>*</strong></span></label>
                    <input type="color" class="form-control" id="update_status_color" name="color" required>
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
      $('.editStatusBtn').on('click', function () {
        const statusId = $(this).data('id');

        // Fetch status data via AJAX
        $.ajax({
          url: `/status/${statusId}/json`,
          method: 'GET',
          success: function (data) {
            $('#update_status_id').val(data.id);
            $('#update_status_name').val(data.name);
            $('#update_status_color').val(data.color);
            $('#status_id').val(data.id);

            // Set form action dynamically
            const formAction = `/status/${data.id}`;
            $('#updateModal form').attr('action', formAction);
            $('#updateModal form').append('<input type="hidden" name="_method" value="PUT">');

            // Open modal
            $.magnificPopup.open({
              items: {
                src: '#updateModal'
              },
              type: 'inline'
            });
          },
          error: function () {
            alert('Failed to fetch project status data.');
          }
        });
      });
    });
  </script>
@endsection
