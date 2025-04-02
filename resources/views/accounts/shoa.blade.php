@extends('layouts.app')

@section('title', 'Accounts | Sub Head Of Accounts')

@section('content')
  <div class="row">
    <div class="col">
      <section class="card">
        <header class="card-header" style="display: flex;justify-content: space-between;">
            <h2 class="card-title">All Sub Head Of Accounts</h2>
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
                    <th>Head</th>
                    <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($subHeadOfAccounts as $item)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->headOfAccount->name ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('shoa.edit', $item->id) }}" class="text-primary"><i class="fa fa-edit"></i></a>
                        <form action="{{ route('shoa.destroy', $item->id) }}" method="POST" style="display:inline;">
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

        <div id="addModal" class="modal-block modal-block-primary mfp-hide">
            <section class="card">
                <form method="post" action="{{ route('shoa.store') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                    @csrf
                    <header class="card-header">
                        <h2 class="card-title">New Sub Head Of Account</h2>
                    </header>
                    <div class="card-body">
                        <div class="form-group mt-2">
                            <label>Head Of Account<span style="color: red;"><strong>*</strong></span></label>
                            <select data-plugin-selecttwo class="form-control select2-js" name ="hoa_id" required>
                                <option value="" selected disabled>Select Head</option>
                                @foreach($HeadOfAccounts as $key => $row)	
                                    <option value="{{$row->id}}">{{$row->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label>Account Group Name<span style="color: red;"><strong>*</strong></span></label>
                            <input type="text" class="form-control" placeholder="Name" name="name" required>
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

        <div id="updateModal" class="modal-block modal-block-primary mfp-hide">
            <section class="card">
                <form method="post" action="" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                    @csrf
                    <header class="card-header">
                        <h2 class="card-title">Update COA Group</h2>
                    </header>
                    <div class="card-body">
                       <div class="form-group">
                            <input type="number" class="form-control" id="update_group_id" required disabled>
                        </div>
                        <div class="form-group">
                            <label>Account Group Name<span style="color: red;"><strong>*</strong></span></label>
                            <input type="text" class="form-control" id="update_group_name" placeholder="Name" name="group_name" required>
                            <input type="hidden" class="form-control" id="group_id" name="group_cod" required>
                        </div>
                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">Update COA Group</button>
                                <button class="btn btn-default modal-dismiss">Cancel</button>
                            </div>
                        </div>
                    </footer>
                </form>
            </section>
        </div>
    </div>
  </div>
@endsection
