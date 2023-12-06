@extends('admin.layout.app')
@section('title', 'New Scan Out')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">New Scan Out</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">New Scan Out</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="scan-out-form" method="post" action="{{ route('admin:newScanOut.add') }}">
                            @csrf
                            <input type="hidden" id="scan_id" value="" name="id">
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label class="mb-2" for="releaseNumber">Release No</label>
                                    <input class="form-control form-control-sm" id="releaseNumber" name="release_number"
                                        type="text" value="">
                                </div>
                                <div class="col-md-4">
                                    <label class="mb-2" for="supplier">Customer</label>
                                    <select class="form-control form-control-sm selected_customer select2"
                                        name="customer_id">
                                        <option value="">Select</option>
                                        @if (isset($customers) & !empty($customers))
                                            @foreach ($customers as $key => $value)
                                                <option value="{{ $value->id }}">
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
                                                <option value="{{ $value->id }}">
                                                    {{ $value->name ?? 'N/A' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">

                                    <label class="mb-2" for="pending_order">Pending Order</label><br />
                                    <input type="radio" name="is_order_pending" value="yes" id="yes_pending_order" />
                                    <label for="yes_pending_order">Yes</label>

                                    <input type="radio" name="is_order_pending" value="no" id="no_pending_order"
                                        checked />
                                    <label for="no_pending_order">No</label>

                                </div>
                            </div>
                            <div class="hide_fields">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="mb-2" for="container">Container </label>
                                        <input class="form-control form-control-sm" id="container" name="container"
                                            type="text" />
                                    </div>
                                    <div class="col-md-4">
                                        <label class="mb-2" for="tear_factor">Tare Factor</label>
                                        <input class="form-control form-control-sm" id="tear_factor" name="tear_factor"
                                            type="number" step="any" />
                                    </div>
                                    <div class="col-md-4">
                                        <label class="mb-2" for="seal">Seal</label>
                                        <input class="form-control form-control-sm" id="seal" name="seal"
                                            type="text" step="any" />
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-md-4">
                                        <label class="mb-2" for="pallet_weight">Pallet Tear</label>
                                        <input class="form-control form-control-sm" id="pallet_weight" name="pallet_weight"
                                            type="text" />
                                    </div>
                                    <div class="col-md-4">
                                        <label class="mb-2" for="tear_factor_weight">Tare Factor 2</label>
                                        <input class="form-control form-control-sm" id="tear_factor_weight"
                                            name="tear_factor_weight" type="number" step="any" />
                                    </div>
                                    <div class="col-md-4">
                                        <label class="mb-2" for="discrepancy">Scale Tickets Weight</label>
                                        <input class="form-control form-control-sm" id="discrepancy"
                                            name="scale_discrepancy" type="number" step="any" />
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-md-4">
                                        <label class="mb-2" for="pallet_on_container">Pallet On Container</label>
                                        <input class="form-control form-control-sm" id="pallet_on_container"
                                            name="pallet_on_container" type="number" step="any" />
                                    </div>
                                </div>
                                <div class="row mt-4 mb-2">
                                    <div class="col-sm-7 col-7">

                                        <div class="header-title">
                                            <h4 class="card-title">Pallet Details</h4>
                                        </div>

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

                                                        {{-- <a href="javascript:void(0)" class="text-danger remove_pallet_row" title="Remove row">
                                                  <i class="far fa-window-close"></i>
                                                </a> --}}
                                                    </td>
                                                </tr>
                                            </tbody>

                                        </table>
                                    </div>
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
                    url: "{{ route('admin:newScanOut.getValues') }}",
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
                $('#add_scan_out_row').trigger('click');
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
        $('#scan-out-form').on('submit', function(event) {

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
                            window.location.href = "{{ route('admin:scanOutLogs') }}";
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
