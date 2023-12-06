@extends('admin.layout.app')
@section('content')
    <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link type="text/css" rel="stylesheet" href="{{ asset('assets/css/image-uploader.min.css') }}">
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin:customers') }}">Customers</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('admin:customers.orders', [$customer_id ?? '']) }}">Customer Orders</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Customer Order Details</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Customer Order Details</h4>
                        </div>

                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td>RELEASE #</td>
                                        <td>{{ $order_history->release_number ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>CUSTOMER</td>
                                        <td>{{ $order_history->getCustomers->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>WAREHOUSE</td>
                                        <td>{{ $order_history->getWareHouse->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>CONTAINER #</td>
                                        <td>{{ $order_history->container ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>ROLLS COUNT</td>
                                        @php
                                            $total_rolls = 0;
                                            foreach ($order_history->getScanOutLogs as $key => $value) {
                                                $total_rolls += $value->scanInLog->rolls;
                                            }
                                        @endphp
                                        <td>{{ $total_rolls ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>TARE FACTOR</td>
                                        <td>{{ $order_history->tear_factor ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>SEAL</td>
                                        <td>{{ $order_history->seal ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>PALLET TEAR</td>
                                        <td>{{ $order_history->pallet_weight ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>TARE FACTOR 2</td>
                                        <td>{{ $order_history->tear_factor_weight ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>SCALE TICKET WEIGHT</td>
                                        <td>{{ $order_history->scale_discrepancy ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>PALLET ON CONTAINER</td>
                                        <td>{{ $order_history->pallet_on_container ?? '-' }}</td>
                                    </tr>

                                    <tr>
                                        <td>ORDER DATE</td>
                                        <td>{{ changeDateFormatToUS($order_history->created_at) ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>STATUS</td>
                                        <td><button type="button" id="inv_status"
                                                class="status-btn btn btn-sm text-center  {{ $order_history->status == 'preloaded' ? 'btn-secondary' : ($order_history->status == 'loaded' ? 'btn-warning' : ($order_history->status == 'shipped' ? 'btn-success' : 'btn-secondary')) }} get_order_id"
                                                data-id="{{ $order_history->id }}"
                                                current-order-status="{{ $order_history->status }}" data-bs-toggle="modal"
                                                data-bs-target="#exampleModalDefault">
                                                {{ $order_history->status }}
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        @if (auth()->check() && session('RoleHasPermission') !== null && session('RoleHasPermission')->nt_grade_column == 1)
                            <div class="row">
                                <div class="col-sm-12 mt-2">
                                    <h4>Prices</h4>

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
                                                @for       else ($nt_grades_prices as $key => $v)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $v->nt_grade ?? '' }}</td>
                                                        <td>
                                                            {{ $v->price ?? '' }}
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
                                Slug, <span class="text-warning">L</span> = Product Type Slug, <span class="text-warning">BK
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
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">

                            <h4 class="card-title">Order Status History</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="status-history" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Previous Status</th>
                                        <th>Changed To</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>User</th>
                                    </tr>
                                </thead>
                                <tbody class="text-dark">
                                    @if (isset($order_history->allOrderStatuses) && !empty($order_history->allOrderStatuses))
                                        @foreach ($order_history->allOrderStatuses as $key => $allOrderStatus)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $allOrderStatus->previous_status == 'preloaded' ? 'Preloaded' : ($allOrderStatus->previous_status == 'loaded' ? 'Loaded' : ($allOrderStatus->previous_status == 'shipped' ? 'Shipped' : '-')) }}
                                                </td>
                                                <td>{{ $allOrderStatus->changed_to == 'preloaded' ? 'Preloaded' : ($allOrderStatus->changed_to == 'loaded' ? 'Loaded' : ($allOrderStatus->changed_to == 'shipped' ? 'Shipped' : '-')) }}
                                                </td>
                                                <td>{{ changeDateFormatToUS($allOrderStatus->created_at) ?? 'N/A' }}
                                                </td>
                                                <td>{{ date('h:i:s a', strtotime($allOrderStatus->created_at)) ?? 'N/A' }}
                                                </td>
                                                <td>{{ $allOrderStatus->getUser->full_name ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">

                            <h4 class="card-title">Order Documents</h4>
                        </div>
                    </div>
                    <div class="card-body card-height">
                        <div class="row">
                            @if ($order_history->OrderFiles && $order_history->OrderFiles->count() > 0)
                                <div class="row">
                                    @foreach ($order_history->OrderFiles as $key => $OrderFile)
                                        <div class="col-md-2">
                                            <div class="card-container mb-3">
                                                <a href="{{ asset('storage/images/orderFiles/' . $OrderFile->file_name) }}"
                                                    target="blank">
                                                    @if (in_array(pathinfo($OrderFile->file_name, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'PNG', 'JPEG', 'JPG']))
                                                        <img class="card-img-top"
                                                            src="{{ asset('storage/images/orderFiles/' . $OrderFile->file_name) }}"
                                                            alt="Image Preview">
                                                    @else
                                                        <div class="card-img-top file-icon">
                                                            <img class="card-img-top"
                                                                src="https://www.pngall.com/wp-content/uploads/2018/05/Files-High-Quality-PNG.png">
                                                        </div>
                                                    @endif
                                                </a>
                                                <div class="card-body text-center">
                                                    <p class="card-text word-wrap"
                                                        title="{{ $OrderFile->file_name ?? 'N/A' }}">
                                                        {{ $OrderFile->file_name ?? 'N/A' }}</p>
                                                    <a href="{{ asset('storage/images/orderFiles/' . $OrderFile->file_name) }}"
                                                        download>
                                                        <span class="badge rounded-pill bg-primary">Download</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="col-md-12 text-dark text-center">
                                    <p>No Document Available</p>
                                </div>
                            @endif

                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="modal fade" id="exampleModalDefault" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" id="form-example-1" action="{{ route('admin:customers.orders.updateOrderScanStatus') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Update Status
                            </h5>
                        </div>
                        <div class="modal-body">
                            <select class="form-control form-control-sm order-status" name="order_status">
                                @if (isset($order_status) & !empty($order_status))
                                    @foreach ($order_status as $key1 => $val)
                                        <option value={{ $val['status'] }}
                                            {{ $val['status'] == $order_history->status ? 'selected' : '' }}>
                                            {{ ucfirst($val['status']) ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <input type="hidden" value="{{ $order_history->status }}" name="previous_status">
                            <div class="checkbox_values d-flex justify-content-between mt-4 d-none">
                                <label for="deposit_received">Deposit received</label>
                                <input type="checkbox" name="deposit_received"
                                    {{ $order_history->status == 'loaded' && $order_status_recent_record->deposit_received == 1 ? 'checked' : '' }}>
                                <label for="trucker_hired">Trucker hired</label>
                                <input type="checkbox" name="trucker_hired"
                                    {{ $order_history->status == 'loaded' && $order_status_recent_record->trucker_hired == 1 ? 'checked' : '' }}>
                            </div>
                        </div>

                        <input type="hidden" name="user_id" id="user_id" value="{{ auth()->user()->id ?? '' }}">
                        <input type="hidden" name="skew_number" id="skew_number"
                            value="{{ $value->skew_number ?? '' }}">
                        <input type="hidden" name="scan_out_inventory_id" id="scan_out_inventory_id" value="">
                        <div class="upload_document d-none">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Upload Documents
                                </h5>
                            </div>
                            <div class="input-field">
                                <div class="input-images-1"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-danger change_status">Change</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="order_delete" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
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
        <script type="text/javascript" src="{{ asset('assets/js/image-uploader.min.js') }}"></script>
        <script type="text/javascript">
            var table = $('#scan-out-history').DataTable({
                // processing: true,
                // serverSide: true,
            });
            var table2 = $('#status-history').DataTable();
            $('.input-images-1').imageUploader();
            // Define the isPreviewableImage function to check if an image is previewable
            function isPreviewableImage(url, callback) {
                var image = new Image();
                image.onload = function() {
                    callback(true);
                };
                image.onerror = function() {
                    callback(false);
                };
                image.src = url;
            }

            $('input[name="images[]"]').on('change', function() {
                // Loop through all uploaded images
                var files = $(this).get(0).files;
                $('.uploaded-image').each(function(index, item) {
                    var file = files[index];
                    var $img = $(this).find('img');
                    var imgSrc = $img.attr('src');
                    var file = $img.attr('title', file.name);
                    // Check if the image is previewable using isPreviewableImage()
                    isPreviewableImage(imgSrc, function(isPreviewable) {
                        if (isPreviewable) {
                            // console.log('Image is previewable: ' + isPreviewable);
                        } else {
                            // console.log('Image is not previewable');
                            $img.attr({
                                'src': 'https://www.pngall.com/wp-content/uploads/2018/05/Files-High-Quality-PNG.png',
                                'style': 'object-fit: contain !important',
                                'title': file.name
                            });
                        }
                    });
                });
            });


            $(document).on('click', '.get_order_id', function() {
                var scan_out_inventory_id = $(this).attr('data-id');
                var current_order_status = $(this).attr('current-order-status');
                if (current_order_status == 'loaded') {
                    $('.checkbox_values').removeClass('d-none');
                    $('input[name="deposit_received"]').prop('required', true);
                    $('input[name="trucker_hired"]').prop('required', true);
                } else {
                    $('.checkbox_values').addClass('d-none');
                    $('input[name="deposit_received"]').prop('required', false);
                    $('input[name="trucker_hired"]').prop('required', false);
                }
                if (current_order_status == 'shipped') {
                    $('.upload_document').removeClass('d-none');
                } else {
                    $('.upload_document').addClass('d-none');
                }
                $('.order-status').val(current_order_status);
                $('#scan_out_inventory_id').val(scan_out_inventory_id);
                $('#exampleModalDefault').modal('show');
            });

            $(document).ready(function() {
                var currentOrderStatus = $('select[name=order_status] option:selected').val();
                if (currentOrderStatus == 'loaded') {
                    $('.checkbox_values').removeClass('d-none');
                } else {
                    $('.checkbox_values').addClass('d-none');
                }
                if (currentOrderStatus == 'shipped') {
                    $('#inv_status').prop('disabled', true);
                    $('.upload_document').removeClass('d-none');
                } else {
                    $('#inv_status').prop('disabled', false);
                    $('.upload_document').addClass('d-none');
                }
            });
            $('.order-status').on('change', function() {
                var orderStatus = $('select[name=order_status] option:selected').val()
                if (orderStatus == 'shipped') {
                    $('.upload_document').removeClass('d-none');
                } else {
                    $('.upload_document').addClass('d-none');
                }
                if (orderStatus == 'loaded') {
                    $('.checkbox_values').removeClass('d-none');
                    $('input[name="deposit_received"]').prop('required', true);
                    $('input[name="trucker_hired"]').prop('required', true);
                } else {
                    $('.checkbox_values').addClass('d-none');
                    $('input[name="deposit_received"]').prop('required', false);
                    $('input[name="trucker_hired"]').prop('required', false);
                }
            });

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
                                // Redirect to the admin orders page
                                window.location.href =  "{{ route('admin:customers.orders', [$customer_id ?? '']) }}";
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
