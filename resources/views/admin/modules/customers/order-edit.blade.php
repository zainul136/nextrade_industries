@extends('admin.layout.app')
@section('title', 'Edit Customer Order History')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin:customers') }}">Customers</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('admin:customers.orders', [$customer_id ?? '']) }}">Customer Orders</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Customer Order</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">

                            <h4 class="card-title">Edit Customer Order</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin:customers.updateOrder', [$customer_id, $order_history->id]) }}"
                            method="POST">
                            @csrf @method('Put')

                            <div class="row">

                                <div class="col-sm-12">

                                    <label class="mb-2" for="releaseNumber"><b>Release No</b></label>
                                    <p>
                                        {{ $order_history->release_number ?? '' }}
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="mb-2" for="supplier">Customer</label>
                                    <select class="form-control form-control-sm selected_customer select2"
                                        name="customer_id">
                                        <option value="">Select</option>
                                        @if (isset($customers) & !empty($customers))
                                            @foreach ($customers as $key => $value)
                                                <option value="{{ $value->id }}"
                                                    {{ $value->id == $order_history->customer_id ? 'selected' : '' }}>
                                                    {{ $value->name ?? 'N/A' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="mb-2" for="warehouse">Warehouse</label>
                                    <select class="form-control form-control-sm select2" name="warehouse_id">
                                        <option value="">Select</option>
                                        @if (isset($warehouses) & !empty($warehouses))
                                            @foreach ($warehouses as $key => $value)
                                                <option value="{{ $value->id }}"
                                                    {{ $value->id == $order_history->warehouse_id ? 'selected' : '' }}>
                                                    {{ $value->name ?? 'N/A' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="container">Container</label>
                                    <input class="form-control form-control-sm" id="container" name="container"
                                        type="text" value="{{ old('container', $order_history->container ?? '') }}" />
                                    @error('container')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label for="tear_factor">Tare Factor</label>
                                    <input class="form-control form-control-sm" id="tear_factor" name="tear_factor"
                                        type="number" step="any"
                                        value="{{ old('tear_factor', $order_history->tear_factor ?? '') }}" />
                                    @error('tear_factor')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="seal">Seal</label>
                                    <input class="form-control form-control-sm" id="seal" name="seal" type="number"
                                        step="any" value="{{ old('seal', $order_history->seal ?? '') }}" />
                                    @error('seal')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="pallet_weight">Pallet Tear</label>
                                    <input class="form-control form-control-sm" id="pallet_weight" name="pallet_weight"
                                        type="number" step="any"
                                        value="{{ old('pallet_weight', $order_history->pallet_weight ?? '') }}" />
                                    @error('pallet_weight')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4 ">
                                    <label for="tear_factor_weight">Tare Factor 2</label>
                                    <input class="form-control form-control-sm" id="tear_factor_weight"
                                        name="tear_factor_weight" type="number" step="any"
                                        value="{{ old('tear_factor_weight', $order_history->tear_factor_weight ?? '') }}" />
                                    @error('tear_factor_weight')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="scale_discrepancy">Scale Tickets Weight</label>
                                    <input class="form-control form-control-sm" id="scale_discrepancy"
                                        name="scale_discrepancy" type="number" step="any"
                                        value="{{ old('scale_discrepancy', $order_history->scale_discrepancy ?? '') }}" />
                                    @error('scale_discrepancy')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="mb-2" for="pallet_on_container">Pallet On Container</label>
                                    <input class="form-control form-control-sm" id="pallet_on_container"
                                        name="pallet_on_container"
                                        value="{{ old('pallet_on_container', $order_history->pallet_on_container ?? '') }}"
                                        type="number" step="any" />
                                </div>
                            </div>
                            @if (auth()->check() && session('RoleHasPermission') !== null && session('RoleHasPermission')->nt_grade_column == 1)
                                <div class="row mt-3">

                                    <div class="col-sm-12">

                                        <h5>Prices</h5>

                                    </div>
                                    <div class="col-sm-12 mt-2">

                                        <div class="table-responsive">
                                            <table id="nt_grade_prices" class="table table-bordered" style="width:50%;">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>NT grade</th>
                                                        <th>Price</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="text-dark">
                                                    @forelse ($nt_grades_prices as $key => $v)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $v->nt_grade ?? '' }}</td>
                                                            <td>
                                                                <input type="hidden" name="nt_id[]"
                                                                    value="{{ $v->nt_id ?? 0 }}" />
                                                                <input class="form-control form-control-sm"
                                                                    id="pallet_weight" name="nt_price[]" type="number"
                                                                    step="any" value="{{ $v->price ?? '' }}" />
                                                            </td>
                                                        </tr>

                                                    @empty
                                                        <tr>
                                                            <th colspan="3">No record found...</th>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            @endif
                            <div class="mt-4 d-flex justify-content-start">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Order Scan Out History</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="mt-2 mb-2 text-info"><small><span class="text-warning">Note: </span> Skew no. Format:
                                e.g: <span class="text-warning">W.3.A.L.BK.21.2500.4 <span class="text-info">/</span>
                                    Y.3.A.L.BK.21.2500.4
                                </span></small></p>
                        <p class="mt-2 mb-3 text-info"><small><span class="text-warning">W </span> = Weight Unit, <span
                                    class="text-warning">Y </span>=
                                Yards Unit, <span class="text-warning">3</span> = CGT Slug, <span
                                    class="text-warning">A</span> = NT
                                Slug, <span class="text-warning">L</span> = Product Type Slug, <span
                                    class="text-warning">BK
                                </span> = Color Slug, <span class="text-warning">21 </span>
                                = # of rolls, <span class="text-warning">2500 </span>= weight/ yards
                                based on Unit, <span class="text-warning">4 </span>= any random number for avoid
                                duplication.</small></p>
                        <div class="table-responsive">
                            <table id="scan-out-history" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Skew #</th>
                                        <th>Customer</th>
                                        <th>Product Type</th>
                                        <th>CGT Grade</th>
                                        @if (auth()->check() && session('RoleHasPermission') !== null && session('RoleHasPermission')->nt_grade_column == 1)
                                            <th>NT Grade</th>
                                        @endif
                                        <th>Color</th>
                                        <th>Weight</th>
                                        <th>Yards</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody class="text-dark">
                                    @if (isset($order_skew_number) && !empty($order_skew_number))
                                        @foreach ($order_skew_number as $key => $value)
                                            <tr>
                                                <td>
                                                    {{ $key + 1 }}
                                                </td>
                                                <td>{{ $value->skew_number ?? 'N/A' }}</td>
                                                <td>{{ $value->customer_name ?? 'N/A' }}</td>
                                                <td>{{ $value->product_type ?? 'N/A' }}</td>
                                                <td>{{ $value->cgt_grade ?? 'N/A' }}</td>
                                                @if (auth()->check() && session('RoleHasPermission') !== null && session('RoleHasPermission')->nt_grade_column == 1)
                                                    <td>{{ $value->nt_grade ?? 'N/A' }}</td>
                                                @endif
                                                <td>{{ $value->color_name ?? 'N/A' }}</td>
                                                <td>{{ $value->weight ?? '-' }}</td>
                                                <td>{{ $value->yards ?? '-' }}</td>
                                                <td><a href="javascript:void(0);" class="delete_skew_popup"
                                                        skew-id="{{ $value->skew_id }}"
                                                        total_records={{ $total_records ?? 0 }} title="Delete">
                                                        <i class="fa fa-trash text-danger"></i>
                                                    </a></td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="order_delete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Confirm Delete
                    </h5>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this Skew Number?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-danger delete_skew" order_records_count=""
                        id="model_id" scan_out_inventory_id="{{ $order_history->id }}">Delete
                        Skew</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

    <script type="text/javascript">
        $(document).on('click', '.delete_skew_popup', function() {
            var id = $(this).attr('skew-id');
            var total_records = $(this).attr('total_records');
            if (total_records == 1) {
                $('.modal-body').html(
                    "By deleting this last skew number the order will also delete. Do you want to proceed?");
            }
            $('#model_id').val(id);
            $('#model_id').attr('order_records_count', total_records);
            $('#order_delete').modal('show');
        });

        $(document).on('click', '.delete_skew', function() {
            var scan_in_id = $(this).val();
            var scan_out_inventory_id = $(this).attr('scan_out_inventory_id')
            var order_records_count = $(this).attr('order_records_count');
            $.ajax({
                type: 'POST',
                url: "{{ route('admin:customers.skewNumberDelete') }}",
                data: {
                    'scan_in_id': scan_in_id,
                    'scan_out_inventory_id': scan_out_inventory_id,
                    'total_records': order_records_count,
                    '_token': "{{ csrf_token() }}"
                },
                success: function(result) {
                    if (result.status === true) {
                        toastr.success(result.message);
                        if (result.order_delete === true) {
                            // Redirect to the admin customers orders page
                            alert($customer_id)
                            window.location.href =
                                "{{ route('admin:customers.orders', [$customer_id ?? '']) }}";
                        } else {
                            // Reload the current page
                            location.reload();
                        }
                    } else {
                        toastr.error(result.message);
                    }

                }
            });
        });
    </script>
@endsection
