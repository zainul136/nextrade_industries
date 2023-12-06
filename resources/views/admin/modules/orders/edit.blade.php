@extends('admin.layout.app')
@section('title', 'Edit Order History')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        @if ($order_history->is_order_pending == 1)
                            <li class="breadcrumb-item active" aria-current="page"><a
                                    href="{{ route('admin:pendingOrders') }}">Pending Orders</a></li>
                        @else
                            <li class="breadcrumb-item active" aria-current="page"><a
                                    href="{{ route('admin:orders') }}">Orders</a></li>
                        @endif
                        <li class="breadcrumb-item active" aria-current="page">Edit Order</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">

                            <h4 class="card-title">Edit Order</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin:orders.updateOrder', [$order_history->id, $customer_id]) }}"
                            method="POST">
                            @csrf @method('Put')
                            <input type="hidden" value="{{ $customer_id }}" name="customer_order">
                            <input type="hidden" name="is_order_pending" value="{{ $order_history->is_order_pending }}">
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
                                    <input class="form-control form-control-sm" id="seal" name="seal" type="text"
                                        value="{{ old('seal', $order_history->seal ?? '') }}" />
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
                            <div class="row mt-3">
                                {{-- @if (auth()->check() && session('RoleHasPermission') !== null && session('RoleHasPermission')->nt_price_column == 1) --}}
                                <div class="col-sm-6">
                                    <h5 class="mb-2">Nextrade Prices</h5>
                                    <div class="table-responsive">
                                        <table id="nt_grade_prices" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>CGT Grade</th>
                                                    <th>Price</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-dark">
                                                @forelse ($nt_grades_prices as $key => $v)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $v->cgt_grade ?? '' }}</td>
                                                        <td>
                                                            <input type="hidden" name="cgt_id[]"
                                                                value="{{ $v->cgt_id ?? 0 }}" />
                                                            <input class="form-control form-control-sm" id="pallet_weight"
                                                                name="cgt_price[]" type="number" step="any"
                                                                value="{{ $v->price ?? '' }}" />
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
                                {{-- @endif --}}
                                @if (auth()->check() &&
                                        session('RoleHasPermission') !== null &&
                                        session('RoleHasPermission')->third_party_price_column == 1)
                                    <div class="col-sm-6 ">

                                        <h5 class="mb-2">Third Party Prices</h5>
                                        <div class="table-responsive">
                                            <table id="nt_grade_prices" class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>NT Grade</th>
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
                                                                    id="pallet_weight" name="third_party_price[]"
                                                                    type="number" step="any"
                                                                    value="{{ $v->third_party_price ?? '' }}" />
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
                                @endif
                            </div>
                            <div class="mt-4 d-flex justify-content-start">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @if (
                $order_history->is_order_pending == 1 &&
                    ($order_history->status != 'shipped' &&
                        $order_history->status != 'post_loading_documentation' &&
                        $order_history->status != 'end_stage' &&
                        $order_history->status != 'closed' &&
                        $order_history->status != 'cancelled'))
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Scan Out Skews</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="order_scan_out_form" method="post"
                                action="{{ route('admin:orders.newScanOut') }}">
                                @csrf
                                <div class="row mt-4 mb-2">
                                    <div class="col-sm-7 col-7">
                                    </div>
                                    <div class="col-sm-5 col-5">

                                        <button type="button" class="btn btn-sm btn-primary" id="add_scan_out_row"
                                            style="float:right;">
                                            <i class="fa fa-plus"></i> Add Row
                                        </button>

                                    </div>
                                </div>
                                <p class="mt-2 mb-2 text-info"><small><span class="text-warning">Note: </span> Skew no.
                                        Format:
                                        e.g: <span class="text-warning">W.3.A.L.BK.21.2500.4 <span
                                                class="text-info">/</span>
                                            Y.3.A.L.BK.21.2500.4
                                        </span></small></p>
                                <p class="mt-2 mb-3 text-info"><small><span class="text-warning">W </span> = Weight Unit,
                                        <span class="text-warning">Y </span>=
                                        Yards Unit, <span class="text-warning">3</span> = CGT Slug, <span
                                            class="text-warning">A</span> = NT
                                        Slug, <span class="text-warning">L</span> = Product Type Slug, <span
                                            class="text-warning">BK
                                        </span> = Color Slug, <span class="text-warning">21
                                        </span>
                                        = # of rolls, <span class="text-warning">2500 </span>= weight/ yards
                                        based on Unit, <span class="text-warning">4 </span>= any random number for avoid
                                        duplication.</small></p>
                                <div class="row mb-4">
                                    <div class="col-sm-12">
                                        <div class="form-check form-switch" style="float: right;">
                                            <label for="automatically_add_new_row">Automatically Add New Row</label>
                                            <input class="form-check-input" type="checkbox" checked
                                                name="automatically_add_new_row" role="switch"
                                                id="automatically_add_new_row">
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-light py-2 px-1" style=" overflow-x: auto; ">
                                    <div class="mt-2">

                                        <table width="100%">
                                            <thead>
                                                <tr>
                                                    <th>Skew</th>
                                                    <th>CGT</th>
                                                    <th>NT</th>
                                                    <th>Product</th>
                                                    <th>Color</th>
                                                    <th>Rolls</th>
                                                    <th>Weight/Yard</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="scan_out_rows_">
                                                <tr>
                                                    <input type="hidden" name="scan_out_inventory_id"
                                                        value="{{ $order_history->id }}">
                                                    <td>
                                                        <input class="unit_val" type="hidden" name="unit[]">
                                                        <input class="form-control form-control-sm skew_no_row1 skew_no_"
                                                            id="skew_no" name="skew_no[]" type="text" />
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="cgt[]" class="cgt_id" />
                                                        <input class="form-control form-control-sm cgt_val" type="text"
                                                            readonly />
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="nt[]" class="nt_id" />
                                                        <input class="form-control form-control-sm nt_val" type="text"
                                                            readonly />
                                                    </td>
                                                    <td>

                                                        <input type="hidden" name="product_type[]"
                                                            class="product_type_id" />
                                                        <input class="form-control form-control-sm product_type_val"
                                                            type="text" readonly />

                                                    </td>
                                                    <td>

                                                        <input type="hidden" name="color[]" class="color_id" />
                                                        <input class="form-control form-control-sm color_val"
                                                            type="text" readonly />

                                                    </td>
                                                    <td>

                                                        <input class="form-control form-control-sm rolls_val"
                                                            name="rolls[]" type="text" readonly />

                                                    </td>
                                                    <td>

                                                        <input class="form-control form-control-sm w_or_y_val"
                                                            name="w_or_y[]" type="text" readonly />

                                                    </td>

                                                    <td class="text-center">


                                                    </td>
                                                </tr>
                                            </tbody>

                                        </table>
                                    </div>
                                </div>

                                <div class="mt-4 d-flex">
                                    <button id="scan-out-submit" type="submit" class="btn btn-sm btn-primary">Submit

                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Order Scan Out History</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="mt-2 mb-2 text-info"><small><span class="text-warning">Note: </span> Skew no.
                                Format:
                                e.g: <span class="text-warning">W.3.A.L.BK.21.2500.4 <span class="text-info">/</span>
                                    Y.3.A.L.BK.21.2500.4
                                </span></small></p>
                        <p class="mt-2 mb-3 text-info"><small><span class="text-warning">W </span> = Weight Unit,
                                <span class="text-warning">Y </span>=
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
                    <button type="button" class="btn btn-sm btn-danger delete_skew" customer_id="{{ $customer_id }}"
                        order_records_count="" id="model_id" scan_out_inventory_id="{{ $order_history->id }}">Delete
                        Skew</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('style')

    <style type="text/css">
        #scan_out_rows_ tr td {
            padding-bottom: 6px;

        }

        input:read-only {
            background-color: #f5f5f5 !important;
        }
    </style>
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
            var pending_status = {{ $order_history->is_order_pending }};
            var customer_id = $(this).attr('customer_id');
            var scan_in_id = $(this).val();
            var scan_out_inventory_id = $(this).attr('scan_out_inventory_id')
            var order_records_count = $(this).attr('order_records_count');
            $.ajax({
                type: 'POST',
                url: "{{ route('admin:orders.skewNumberDelete') }}",
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
                            if (pending_status == 1) {
                                // Redirect to the admin Pending orders page
                                window.location.href = "{{ route('admin:pendingOrders') }}";
                            } else {
                                // Redirect to the admin orders page
                                window.location.href = "{{ route('admin:orders') }}" + '/' +
                                    customer_id;
                            }
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

        // when press tab
        $(document).on('keydown', '.skew_no_', function(e) {
            var code = e.keyCode || e.which;

            if (code === 9) {
                e.preventDefault();

                var skew_no = $(this).val()

                var skew_tr = $(this).closest('tr')

                let skew_no_length = skew_no.split('.');

                if (skew_no_length.length == 8) {
                    $('#add_scan_out_row').trigger('click')

                    $(this).closest('tr').next().find('.skew_no_').focus()

                }

            }
        });
        var counter = 1;

        $(document).on('click', '#add_scan_out_row', function() {
            ++counter
            // // HTML code for the new Skew row

            let html = `<tr>
              <td>
                <input class="unit_val" type="hidden" name="unit[]">
                <input class="form-control form-control-sm skew_no_" id="skew_no" name="skew_no[]" type="text"
                    />
              </td>
              <td>
                <input type="hidden" name="cgt[]" class="cgt_id" />
                <input class="form-control form-control-sm cgt_val" type="text" readonly/>
              </td>
              <td>
                <input type="hidden" name="nt[]" class="nt_id" />
                <input class="form-control form-control-sm nt_val" type="text" readonly/>
              </td>
              <td>

                <input type="hidden" name="product_type[]" class="product_type_id" />
                <input class="form-control form-control-sm product_type_val" type="text" readonly/>

              </td>
              <td>

                <input type="hidden" name="color[]" class="color_id" />
                <input class="form-control form-control-sm color_val" type="text" readonly/>

              </td>
              <td>

                <input class="form-control form-control-sm rolls_val" name="rolls[]" type="text" readonly/>

              </td>
              <td>

                <input class="form-control form-control-sm w_or_y_val" name="w_or_y[]" type="text" readonly/>

              </td>

              <td class="text-center">

                    <a href="javascript:void(0)" class="text-danger remove_scan_out_row" title="Remove row" >
                      <i class="far fa-window-close"></i>
                    </a>
              </td>
            </tr>`;

            $('#scan_out_rows_').append(html);

        });

        // when skew no# change
        $(document).on('keyup', '.skew_no_', function() {
            var skew_no = $(this).val()

            var skew_tr = $(this).closest('tr')

            let skew_no_length = skew_no.split('.');

            if (skew_no_length.length == 8) {

                $.ajax({
                    type: 'POST',
                    url: "{{ route('admin:orders.getValues') }}",
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'skew_no': skew_no
                    },
                    success: function(result) {
                        if (result.status == true) {
                            //Displaying Values:
                            skew_tr.find('.unit_val').val(result.data.unit);

                            skew_tr.find('.cgt_val').val(result.data.cgtGrade ?
                                result.data.cgtGrade.grade_name : '');

                            skew_tr.find('.nt_val').val(result.data.ntGrade ?
                                result.data.ntGrade.grade_name : '');

                            skew_tr.find('.product_type_val').val(result.data
                                .productType ?
                                result.data.productType.product_type : '');

                            skew_tr.find('.color_val').val(result.data.color ?
                                result.data.color.name : '');

                            skew_tr.find('.rolls_val').val(result.data.rolls);

                            skew_tr.find('.w_or_y_val').val(result.data.w_or_y);

                            //assigning ids:

                            skew_tr.find('.cgt_id').val(result.data.cgtGrade.id);
                            skew_tr.find('.nt_id').val(result.data.ntGrade.id);
                            skew_tr.find('.product_type_id').val(result.data.productType.id);
                            skew_tr.find('.color_id').val(result.data.color.id);
                            append_row_automatically();
                        }
                    }
                });
            } else {

                skew_tr.find(
                    '.unit_val,.cgt_val,.nt_val,.product_type_val,.color_val,.rolls_val,.w_or_y_val,.cgt_id,.nt_id,.product_type_id,.color_id'
                ).val('')

            }

        });

        function append_row_automatically() {
            if ($('#automatically_add_new_row').is(':checked')) {
                if (!$('.skew_no_').last().val()) { // check if last row is empty
                    return; // exit function
                }
                $('#add_scan_out_row').trigger('click')
                $(this).closest('tr').next().find('.skew_no_').focus();
            }
        }

        // remove pallet row
        $(document).on('click', '.remove_scan_out_row', function() {
            $(this).closest('tr').remove();
        });

        $('input[name=is_order_pending]').on('change', function() {
            if ($(this).val() == 'yes') {
                $('.hide_fields').addClass('hide');
            } else {
                $('.hide_fields').removeClass('hide');
            }
        });
        // submit form
        $('#order_scan_out_form').on('submit', function(event) {

            event.preventDefault();
            // check all skew_numbers its unique or not
            let is_unique = checkAllSkewNumberIsUnique()

            if (is_unique.status == false) {
                toastr.error(is_unique.msg);
            } else {

                var form = $(this);
                var button = $('#scan-out-submit');
                var url = form.attr('action');
                var formData = form.serialize();

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formData,
                    beforeSend: function() {
                        button.prop('disabled', true);
                        button.html('Submitting...');
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.msg);
                            window.location.href = "{{ route('admin:pendingOrders') }}";
                        } else {

                            toastr.error(response.msg);

                        }
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        toastr.error(
                            'An error occurred while submitting the form. Please try again.'
                        );
                    },
                    complete: function() {
                        button.prop('disabled', false);
                        button.html('Submit');

                    }
                });

            }
        });


        // get skew numbers and check all are unique or not
        function checkSkewNumberIsUniqueArr(arr, val) {

            var count_val = 0
            for (var i = 0; i < arr.length; i++) {

                if (val == arr[i]) {
                    count_val += 1
                }

            }

            if (count_val > 1) {
                return true;
            } else {
                return false
            }

        }

        function checkAllSkewNumberIsUnique() {

            var skew_arr = [];
            isDuplicate = false;
            $('.skew_no_').each(function() {

                if ($(this).val()) {

                    skew_arr.push($(this).val())

                }

            })

            if (skew_arr.length > 0) {
                for (var i = 0; i < skew_arr.length; i++) {
                    let res = checkSkewNumberIsUniqueArr(skew_arr, skew_arr[i])
                    if (res) {
                        isDuplicate = true; // Set the flag if duplicates found
                        $('.skew_no_').eq(i).addClass(
                            'duplicate-row'); // Add class to mark the row with duplicate Skew Number
                    } else {
                        $('.skew_no_').eq(i).removeClass('duplicate-row'); // Remove class if no longer a duplicate
                    }
                }
                if (isDuplicate) {
                    return {
                        status: false,
                        msg: 'Duplicate Skew Numbers found. Please fix the duplicates.'
                    };
                } else {
                    return {
                        status: true,
                        msg: ''
                    };
                }
            } else {
                return {
                    status: true,
                    msg: ''
                };
            }

            console.log(skew_arr)

        }
    </script>
@endsection
