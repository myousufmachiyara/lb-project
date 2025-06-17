@extends('layouts.app')

@section('title', 'Services | All Services')

@section('content')
<div class="row">
  <div class="col">
    <section class="card">
      <header class="card-header d-flex justify-content-between">
        <h2 class="card-title">All Services</h2>
        <div>
          <button type="button" class="modal-with-form btn btn-primary" href="#addModal">
            <i class="fas fa-plus"></i> Add New
          </button>
        </div>
      </header>

      <div class="card-body">
        <div class="modal-wrapper table-scroll">
          <table class="table table-bordered table-striped mb-0" id="cust-datatable-default">
            <thead>
              <tr>
                <th>S.No</th>
                <th>Name</th>
                <th>Description</th>
                <th>Charges</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($services as $item)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->description ?? 'N/A' }}</td>
                <td>{{ number_format($item->price, 2) }}</td>
                <td>
                  {{-- Edit Modal trigger --}}
                  <button type="button" class="text-primary bg-transparent border-0" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}">
                    <i class="fa fa-edit"></i>
                  </button>

                  {{-- Delete --}}
                  <form action="{{ route('services.destroy', $item->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button class="text-danger bg-transparent border-0" onclick="return confirm('Are you sure?')">
                      <i class="fa fa-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>

              <!-- Edit Modal -->
              <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1">
                <div class="modal-dialog">
                  <form action="{{ route('services.update', $item->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                      <header class="modal-header">
                        <h5 class="modal-title">Edit Service</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </header>
                      <div class="modal-body">
                        <div class="form-group mb-3">
                          <label>Name <span class="text-danger">*</span></label>
                          <input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
                        </div>
                        <div class="form-group mb-3">
                          <label>Description</label>
                          <textarea name="description" class="form-control">{{ $item->description }}</textarea>
                        </div>
                        <div class="form-group mb-3">
                          <label>Charges (PKR)</label>
                          <input type="number" step="0.01" name="price" class="form-control" value="{{ $item->price }}">
                        </div>
                      </div>
                      <footer class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      </footer>
                    </div>
                  </form>
                </div>
              </div>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <!-- Add Modal -->
    <div id="addModal" class="modal-block modal-block-primary mfp-hide">
      <section class="card">
        <form method="POST" action="{{ route('services.store') }}" onkeydown="return event.key != 'Enter';">
          @csrf
          <header class="card-header">
            <h2 class="card-title">Add New Service</h2>
          </header>
          <div class="card-body">
            <div class="form-group mb-3">
              <label>Name <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control" placeholder="Service Name" required>
              @error('name')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
            <div class="form-group mb-3">
              <label>Description</label>
              <textarea name="description" class="form-control" placeholder="Service Description"></textarea>
              @error('description')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
            <div class="form-group mb-3">
              <label>Charges (PKR)</label>
              <input type="number" step="0.01" name="price" class="form-control" placeholder="Price">
              @error('price')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
          </div>
          <footer class="card-footer">
            <div class="text-end">
              <button type="submit" class="btn btn-success">Add Service</button>
              <button type="button" class="btn btn-default modal-dismiss">Cancel</button>
            </div>
          </footer>
        </form>
      </section>
    </div>
  </div>
</div>
@endsection
