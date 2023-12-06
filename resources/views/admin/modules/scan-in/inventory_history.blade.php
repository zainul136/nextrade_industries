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
                        <li class="breadcrumb-item active" aria-current="page"><a
                                href="{{ route('admin:scanInInventory') }}">Inventory</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Inventory History</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Inventory Details</h4>
                        </div>

                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td>REFERENCE #</td>
                                        <td>{{ $scan_in_history->reference_number ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>SUPPLIER</td>
                                        <td>{{ $scan_in_history->supplier->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>WAREHOUSE</td>
                                        <td>{{ $scan_in_history->warehouse->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>NEXPAC BILL</td>
                                        <td>{{ $scan_in_history->container ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>SCAN IN DATE</td>
                                        <td>{{ changeDateFormatToUS($scan_in_history->created_at) ?? 'N/A' }}</td>
                                    </tr>
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
                            <h4 class="card-title">Scan In History</h4>
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
                            <table id="scan-in-history" class="table table-bordered">
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
                                        <th>Rolls</th>
                                        <th>Weight</th>
                                        <th>Yards</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody class="text-dark">
                                    @if (isset($scanin_skew_number) && !empty($scanin_skew_number))
                                        @foreach ($scanin_skew_number as $key => $value)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $value->skew_number ?? 'N/A' }}</td>
                                                <td>{{ $value->supplier_name ?? 'N/A' }}</td>
                                                <td>{{ $value->product_type ?? 'N/A' }}</td>
                                                <td>{{ $value->cgt_grade ?? 'N/A' }}</td>
                                                @if (auth()->check() && session('RoleHasPermission') !== null && session('RoleHasPermission')->nt_grade_column == 1)
                                                    <td>{{ $value->nt_grade ?? 'N/A' }}</td>
                                                @endif
                                                <td>{{ $value->color_name ?? 'N/A' }}</td>
                                                <td>{{ $value->rolls ?? 'N/A' }}</td>
                                                <td>{{ $value->weight ?? '-' }}</td>
                                                <td>{{ $value->yards ?? '-' }}</td>
                                                <td>
                                                    @if ($value->is_scan_out == 0)
                                                        <a href="javascript:void(0);" class="delete_skew_popup"
                                                            skew-id="{{ $value->skew_id }}"
                                                            total_records={{ $total_records ?? 0 }} title="Delete">
                                                            <i class="fa fa-trash text-danger"></i>
                                                        </a>
                                                    @else
                                                        {{ 'No Action Available' }}
                                                    @endif
                                                </td>
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

        <div class="modal fade" id="record_delete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                        <button type="button" class="btn btn-sm btn-danger delete_skew" records_count="" id="model_id"
                            scan_in_inventory_id="{{ $scan_in_history->id }}">Delete
                            Skew</button>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('script')
        <script type="text/javascript" src="{{ asset('assets/js/image-uploader.min.js') }}"></script>
        <script type="text/javascript">
            var table = $('#scan-in-history').DataTable({
                // processing: true,
                // serverSide: true,
            });


            $(document).on('click', '.delete_skew_popup', function() {
                var id = $(this).attr('skew-id');
                var total_records = $(this).attr('total_records');
                if (total_records == 1) {
                    $('.modal-body').html(
                        "By deleting this last skew number this Scanned In Inventory will also delete. Do you want to proceed?"
                    );
                }
                $('#model_id').val(id);
                $('#model_id').attr('records_count', total_records);
                $('#record_delete').modal('show');
            });

            $(document).on('click', '.delete_skew', function() {
                var scan_in_id = $(this).val();
                var scan_in_inventory_id = $(this).attr('scan_in_inventory_id')
                var records_count = $(this).attr('records_count');
                $.ajax({
                    type: 'POST',
                    url: "{{ route('admin:ScanInInventory.skewNumberDelete') }}",
                    data: {
                        'scan_in_id': scan_in_id,
                        'scan_in_inventory_id': scan_in_inventory_id,
                        'total_records': records_count,
                        '_token': "{{ csrf_token() }}"
                    },
                    success: function(result) {
                        if (result.status === true) {
                            toastr.success(result.message);
                            if (result.record_delete === true) {
                                // Redirect to the admin Inventory page
                                window.location.href = "{{ route('admin:scanInInventory') }}";
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
