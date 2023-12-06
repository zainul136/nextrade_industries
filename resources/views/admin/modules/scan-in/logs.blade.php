@extends('admin.layout.app')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Scan In Details</li>
                    </ol>
                </nav>
            </div>
            <input type="hidden" id="is_nt_column_hide"
                value="{{ auth()->check() &&
                session('RoleHasPermission') !== null &&
                session('RoleHasPermission')->nt_grade_column == 1
                    ? 'no'
                    : 'yes' }}">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Scan In Details</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="mb-5" id="scan_in_filter_form" method="post" action="#">
                            @csrf
                            <div class="row mb-4">

                                <div class="col-md-4">
                                    <label class="mb-2" for="warehouses"> Warehouses </label>
                                    <select class="form-control form-control-sm warehouses select2" name="warehouse_id">
                                        <option value=""> All </option>
                                        @if (isset($warehouses) & !empty($warehouses))
                                            @foreach ($warehouses as $key => $v)
                                                <option value="{{ $v->id }}">
                                                    {{ $v->name ?? '' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="mb-2" for="supplier">Suppliers </label>
                                    <select class="form-control form-control-sm select2" name="supplier_id">
                                        <option value=""> All </option>
                                        @isset($suppliers)
                                            @foreach ($suppliers as $key => $v)
                                                <option value="{{ $v->id }}">
                                                    {{ $v->name ?? '' }}
                                                </option>
                                            @endforeach
                                        @endisset
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="mb-2" for="year"> Year </label>
                                    <select class="form-control form-control-sm year select2" name="year">
                                        <option value=""> All</option>
                                        @foreach (getListOfYears() as $key => $year)
                                            <option value="{{ $year }}"> {{ $year }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-4">

                                <div class="col-md-4">
                                    <label class="mb-2" for="reference_number"> Reference Number </label>
                                    <select class="form-control form-control-sm reference_number select2"
                                        name="reference_number">
                                        <option value=""> All </option>
                                        @if (isset($reference_number) & !empty($reference_number))
                                            @foreach ($reference_number as $key => $v)
                                                <option value="{{ $v->reference_number }}">
                                                    {{ $v->reference_number ?? '' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="mb-2" for="cgt">CGT Grade </label>
                                    <select class="form-control form-control-sm select2" name="cgt">
                                        <option value=""> All </option>
                                        @isset($cgt)
                                            @foreach ($cgt as $key => $v)
                                                <option value="{{ $v->id }}">
                                                    {{ $v->grade_name ?? '' }}
                                                </option>
                                            @endforeach
                                        @endisset
                                    </select>
                                </div>
                                @if (auth()->check() && session('RoleHasPermission') !== null && session('RoleHasPermission')->nt_grade_column == 1)
                                    <div class="col-md-4">
                                        <label class="mb-2" for="nt"> NT Grade </label>
                                        <select class="form-control form-control-sm nt select2" name="nt">
                                            <option value=""> All</option>
                                            @isset($nt)
                                                @foreach ($nt as $key => $v)
                                                    <option value="{{ $v->id }}">
                                                        {{ $v->grade_name ?? '' }}
                                                    </option>
                                                @endforeach
                                            @endisset
                                        </select>
                                    </div>
                                @endif
                                <div class="col-md-4">
                                    <label class="mb-2" for="color"> Color </label>
                                    <select class="form-control form-control-sm nt select2" name="color">
                                        <option value=""> All</option>
                                        @isset($color)
                                            @foreach ($color as $key => $v)
                                                <option value="{{ $v->id }}">
                                                    {{ $v->name ?? '' }}
                                                </option>
                                            @endforeach
                                        @endisset
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="mb-2" for="From"> From </label>
                                    <input type="date" name="from_date" class=" form-control form-control-sm from mr-20"
                                        value="id=&quot;datepicker&quot;">
                                </div>
                                <div class="col-md-4">
                                    <label class="mb-2" for="To"> To </label>
                                    <input type="date" name="to_date" class="form-control form-control-sm to"
                                        value="id=&quot;datepicker&quot;">
                                </div>

                                {{-- <div class="col-md-2">
                                    <button type="button" class=" reset text-center btn btn-primary btn-icon">
                                        <span>reset</span>
                                    </button>
                                    </div>  --}}

                            </div>
                        </form>
                        <div class="table-responsive">
                            <table id="scan-in-logs" class="table table-bordered">
                                <thead>
                                    <tr class="ligth">
                                        <th>#</th>
                                        <th>Reference No</th>
                                        <th>Warehouse</th>
                                        <th>Supplier</th>
                                        <th>NEXPAC Bill</th>
                                        <th>Skew No</th>
                                        <th>Roll #</th>
                                        <th>Weight</th>
                                        <th>Yards</th>
                                        <th>Product</th>
                                        <th>Color</th>
                                        <th>CGT Grade</th>
                                        <th>NT Grade</th>
                                        <th>Date</th>
                                        <th>Scanned Out</th>
                                        <th>Release Number</th>
                                        <th>Release Date</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        // check year is all or not
        $('select[name=year]').change(function() {
            let val = $(this, 'option:selected').val()

            if (val != '') {

                $('input[name=from_date],input[name=to_date]').val('').prop('disabled', true)

            } else {

                $('input[name=from_date],input[name=to_date]').val('').prop('disabled', false)

            }

        })

        var is_nt_column_hide = $('#is_nt_column_hide').val();
        var scan_in_logs_table = $('#scan-in-logs').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin:scanInLogs') }}",
                data: function(d) {
                    d.year = $('select[name=year] option:selected').val(),
                        d.warehouse_id = $('select[name=warehouse_id] option:selected').val(),
                        d.supplier_id = $('select[name=supplier_id] option:selected').val(),
                        d.from_date = $('input[name=from_date]').val(),
                        d.to_date = $('input[name=to_date]').val(),
                        d.reference_number = $('select[name=reference_number]').val(),
                        d.cgt = $('select[name=cgt]').val(),
                        d.nt = $('select[name=nt]').val(),
                        d.color = $('select[name=color]').val()
                }
            },
            lengthMenu: [10, 25, 50, 100, 500],
            // "language": {
            //     search: '<i class="fa fa-search" aria-hidden="true"></i>',
            //     searchPlaceholder: 'filter records',
            //     oPaginate: {
            //         sNext: '<span aria-hidden="true">&raquo;</span>',
            //         sPrevious: '<span aria-hidden="true">&laquo;</spanW>'
            //     }
            // },
            columns: [
                // data = array index & name = database column name
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'get_scan_in_inventory.reference_number',
                    name: 'get_scan_in_inventory.reference_number',
                    render: function(data, type, full, meta) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: 'get_scan_in_inventory.warehouse.name',
                    name: 'get_scan_in_inventory.warehouse.name',
                    render: function(data, type, full, meta) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: 'get_scan_in_inventory.supplier.name',
                    name: 'get_scan_in_inventory.supplier.name',
                    render: function(data, type, full, meta) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: 'get_scan_in_inventory.nexpac_bill',
                    name: 'get_scan_in_inventory.nexpac_bill',
                    render: function(data, type, full, meta) {
                        return data ? data : '-';
                    }
                },
                {
                    data: 'skew_number',
                    name: 'skew_number',
                    render: function(data, type, full, meta) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: 'rolls',
                    name: 'rolls',
                    render: function(data, type, full, meta) {
                        return data ? data : '0';
                    }
                },
                {
                    data: 'weight',
                    name: 'weight',
                    render: function(data, type, full, meta) {
                        return data ? data : '-';
                    }
                },
                {
                    data: 'yards',
                    name: 'yards',
                    render: function(data, type, full, meta) {
                        return data ? data : '-';
                    }
                },
                {
                    data: 's_i_product_name.product_type',
                    name: 's_i_product_name.product_type',
                    render: function(data, type, full, meta) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: 's_i_color.name',
                    name: 's_i_color.name',
                    render: function(data, type, full, meta) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: 's_i_c_g_t.grade_name',
                    name: 's_i_c_g_t.grade_name',
                    render: function(data, type, full, meta) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: 's_i_n_t.grade_name',
                    name: 's_i_n_t.grade_name',
                    visible: is_nt_column_hide == 'yes' ? false : true,
                    render: function(data, type, full, meta) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: 'scan_in_inv_date',
                    name: 'get_scan_in_inventory.created_at',
                },
                {
                    data: 'scan_out_status',
                    name: 'is_scan_out',
                },
                {
                    data: 'get_scan_out_logs.scan_out_inventory.release_number',
                    name: 'get_scan_out_logs.scan_out_inventory.release_number',
                    render: function (data, type, full, meta) {
                        return data ? data : '-';
                    }
                },
                {
                    data: 'get_scan_out_logs.scan_out_inventory.created_at',
                    name: 'get_scan_out_logs.scan_out_inventory.created_at',
                    render: function (data, type, row) {
                        if (type === 'display' || type === 'filter') {
                            // Format the date using JavaScript
                            var date = new Date(data);
                            var formattedDate = date.toLocaleDateString('en-GB'); // Format to 'dd-MM-yyyy'
                            return formattedDate;
                        } else {
                            return data;
                        }
                    },
                },
            ],
            rowCallback: function(row, data) {
                if (data.is_scan_out == 1) {
                    $(row).addClass('bg-light');
                }
            }
        });

        $('select[name=year],select[name=warehouse_id],select[name=supplier_id],input[name=from_date],input[name=to_date],select[name=reference_number],select[name=cgt],select[name=nt],select[name=color]')
            .change(function() {

                scan_in_logs_table.draw();

            })
    </script>
@endsection
