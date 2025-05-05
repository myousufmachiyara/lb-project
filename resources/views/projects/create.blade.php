@extends('layouts.app')

@section('title', 'Projects | New Project')

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
                    <h2 class="card-title">Add New Projects</h2>
                </header>
                <div class="card-body">
                    <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-md-3 mb-3">
                                <label class="form-label">Project Name</label>
                                <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                                @error('name')<div class="text-danger">{{ $message }}</div>@enderror

                            </div>
                            
                            <div class="col-12 col-md-2 mb-3">
                                <label class="form-label">Total Pieces</label>
                                <input type="number" name="total_pcs" class="form-control" required value="{{ old('total_pcs') }}">
                                @error('total_pcs')<div class="text-danger">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-md-3 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status_id" class="form-select" required>
                                    <option value="">-- Select Status --</option>
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->id }}" {{ old('status_id') == $status->id ? 'selected' : '' }}>
                                            {{ $status->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status_id')<div class="text-danger">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label class="form-label">Attachments</label>
                                <input type="file" name="attachments[]" class="form-control" multiple accept="image/*">
                                @if ($errors->has('attachments.*'))
                                    @foreach ($errors->get('attachments.*') as $messages)
                                        @foreach ($messages as $message)
                                            <div class="text-danger">{{ $message }}</div>
                                        @endforeach
                                    @endforeach
                                @endif                            
                            </div>
                            <div class="col-12 col-md-5 mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
                                @error('description')<div class="text-danger">{{ $message }}</div>@enderror
                            </div>
                            <div id="preview-images" class="mt-2"></div>

                            <footer class="card-footer text-end mt-2">
                                <a class="btn btn-danger" href="{{ route('projects.index') }}">Discard</a>
                                <button type="submit" class="btn btn-primary">Create</button>
                            </footer>
                        </div>
                    </form>
                </div>
            </section>


            <section class="card">
          <header class="card-header">
						<div style="display: flex;justify-content: space-between;">
              <h2 class="card-title">Item Details</h2>
						</div>
						@if ($errors->has('error'))
							<strong class="text-danger">{{ $errors->first('error') }}</strong>
						@endif
					</header>
          <div class="card-body" style="max-height:400px; overflow-y:auto">
            <table class="table table-bordered" id="myTable">
              <thead>
                <tr>
                  <th width="2%">Item Name</th>
                  <th>Width</th>
                  <th>Description</th>
                  <th>Rate</th>
                  <th>Quantity</th>
                  <th>Unit</th>
                  <th>Total</th>
                  <th></th>
                </tr>
              </thead>
              <tbody id="PurPOTbleBody">
                <tr>
                  <td width="25%">
                    <select data-plugin-selecttwo class="form-control select2-js" id="productSelect1" onchange="updateUnit(1)" name="details[0][item_id]" required>  <!-- Added name attribute for form submission -->
                      <option value="" selected disabled>Select Item</option>

                    </select>  
                  </td>
                  <td><input type="number" name="details[0][width]"  id="item_width1" step="any" class="form-control" placeholder="Width" required/></td>
                  <td><input type="text" name="details[0][description]"  id="item_description1" class="form-control" placeholder="Description"/></td>
                  <td><input type="number" name="details[0][item_rate]"  id="item_rate1" onchange="rowTotal(1)" step="any" value="0" class="form-control" placeholder="Rate" required/></td>
                  <td>
                    <input type="number" name="details[0][item_qty]" id="item_qty1" onchange="rowTotal(1)" step="any" class="form-control" placeholder="Quantity" required/>
                  </td>
                  <td>
                    <input type="text" id="unitSuffix1" class="form-control" placeholder="unit" disabled/>
                  </td>
                  <td><input type="number" id="item_total1" class="form-control" placeholder="Total" disabled/></td>
                  <td>
										<button type="button" onclick="removeRow(this)" class="btn btn-danger" tabindex="1"><i class="fas fa-times"></i></button>
                    <button type="button" class="btn btn-primary" onclick="addNewRow()" ><i class="fa fa-plus"></i></button></td>
                </tr>
              </tbody>
            </table>
          </div>

          <footer class="card-footer">
            <div class="row">
              <div class="col-12 col-md-2">
                <label>Total Quantity</label>
                <input type="number" class="form-control" id="total_qty" placeholder="Total Quantity" disabled/>
              </div>
              <div class="col-12 col-md-2">
                <label>Total Amount</label>
                <input type="number" class="form-control" id="total_amt" placeholder="Total Amount" disabled />
              </div>
              <div class="col-12 col-md-2">
                <label>Other Expenses</label>
                <input type="number" class="form-control" name="other_exp" id="other_exp" onchange="netTotal()" value=0 placeholder="Other Expenses" />
              </div>
              <div class="col-12 col-md-2">
                <label>Bill Discount</label>
                <input type="number" class="form-control" name="bill_discount" id="bill_disc" onchange="netTotal()" value=0 placeholder="Bill Discount"  />
              </div>
              <div class="col-12 pb-sm-3 pb-md-0 text-end">
                <h3 class="font-weight-bold mt-3 mb-0 text-5 text-primary">Net Amount</h3>
                <span>
                  <strong class="text-4 text-primary">PKR <span id="netTotal" class="text-4 text-danger">0.00 </span></strong>
                </span>
              </div>
            </div>
          </footer>
          <footer class="card-footer text-end">
            <a class="btn btn-danger" href="{{ route('pur-pos.index') }}" >Discard</a>
            <button type="submit" class="btn btn-primary">Create</button>
          </footer>
        </section>
            
        </div>
    </div>
    <script>
        function previewImages(event) {
            const preview = document.getElementById('preview-images');
            preview.innerHTML = '';
            Array.from(event.target.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'me-2 mb-2';
                    img.style.maxHeight = '100px';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        }
    </script>
@endsection