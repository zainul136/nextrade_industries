@extends('admin.layout.app')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Scan Out Details</li>
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
                            <h4 class="card-title">Scan Out Details</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- Filter --}}
                        <form class="mb-5" id="scan_out_filter_form" method="post" action="#">
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
                                    <label class="mb-2" for="Customer"> Customers </label>
                                    <select class="form-control form-control-sm customer_id select2" name="customer_id">
                                        <option value=""> All </option>
                                        @isset($customers)
                                            @foreach ($customers as $key => $v)
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
                                        <option value=""> All </option>
                                        @foreach (getListOfYears() as $key => $year)
                                            <option value="{{ $year }}"> {{ $year }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-4">

                                <div class="col-md-4">
                                    <label class="mb-2" for="release_number"> Release Number </label>
                                    <select class="form-control form-control-sm release_number select2"
                                        name="release_number">
                                        <option value=""> All </option>
                                        @if (isset($release_number) & !empty($release_number))
                                            @foreach ($release_number as $key => $v)
                                                <option value="{{ $v->release_number }}">
                                                    {{ $v->release_number ?? '' }}
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
                            <table id="scan-out-logs" class="table table-bordered">
                                <thead>
                                    <tr class="ligth">
                                        <th>#</th>
                                        <th>Release No</th>
                                        <th>Customer</th>
                                        <th>Warehouse</th>
                                        <th>Container</th>
                                        {{-- <th>Tare Factor</th> --}}
                                        <th>Seal</th>
                                        {{-- <th>Pallet Tare</th> --}}
                                        {{-- <th>Tare Factor 2</th> --}}
                                        {{-- <th>Scale Tickets Weight</th> --}}
                                        {{-- <th>Pallet On Container</th> --}}
                                        <th>Skew</th>
                                        <th>Roll #</th>
                                        <th>Weight</th>
                                        <th>Yards</th>
                                        <th>Product</th>
                                        <th>Color</th>
                                        <th>CGT Grade</th>
                                        <th>NT Grade</th>
                                        <th>Date</th>

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
        var scan_out_logs_table = $('#scan-out-logs').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin:scanOutLogs') }}",
                data: function(d) {
                    d.year = $('select[name=year] option:selected').val(),
                        d.warehouse_id = $('select[name=warehouse_id] option:selected').val(),
                        d.customer_id = $('select[name=customer_id] option:selected').val(),
                        d.from_date = $('input[name=from_date]').val(),
                        d.to_date = $('input[name=to_date]').val(),
                        d.release_number = $('select[name=release_number]').val(),
                        d.cgt = $('select[name=cgt]').val(),
                        d.nt = $('select[name=nt]').val(),
                        d.color = $('select[name=color]').val()
                }
            },
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
                    data: 'scan_out_inventory.release_number',
                    name: 'scan_out_inventory.release_number',
                    render: function(data, type, full, meta) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: 'scan_out_inventory.get_customers.name',
                    name: 'scan_out_inventory.get_customers.name',
                    render: function(data, type, full, meta) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: 'scan_out_inventory.get_ware_house.name',
                    name: 'scan_out_inventory.get_ware_house.name',
                    render: function(data, type, full, meta) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: 'scan_out_inventory.container',
                    name: 'scan_out_inventory.container',
                    render: function(data, type, full, meta) {
                        return data ? data : '-';
                    }
                },
                // {
                //     data: 'scan_out_inventory.tear_factor',
                //     name: 'scan_out_inventory.tear_factor',
                //     render: function(data, type, full, meta) {
                //         return data ? data : '-';
                //     }
                // },
                {
                    data: 'scan_out_inventory.seal',
                    name: 'scan_out_inventory.seal',
                    render: function(data, type, full, meta) {
                        return data ? data : '-';
                    }
                },
                // {
                //     data: 'scan_out_inventory.pallet_weight',
                //     name: 'scan_out_inventory.pallet_weight',
                //     render: function(data, type, full, meta) {
                //         return data ? data : '-';
                //     }
                // },
                // {
                //     data: 'scan_out_inventory.tear_factor_weight',
                //     name: 'scan_out_inventory.tear_factor_weight',
                //     render: function(data, type, full, meta) {
                //         return data ? data : '-';
                //     }
                // },
                // {
                //     data: 'scan_out_inventory.scale_discrepancy',
                //     name: 'scan_out_inventory.scale_discrepancy',
                //     render: function(data, type, full, meta) {
                //         return data ? data : '-';
                //     }
                // },
                // {
                //     data: 'scan_out_inventory.pallet_on_container',
                //     name: 'scan_out_inventory.pallet_on_container',
                //     render: function(data, type, full, meta) {
                //         return data ? data : '-';
                //     }
                // },
                {
                    data: 'scan_in_log.skew_number',
                    name: 'scan_in_log.skew_number',
                    render: function(data, type, full, meta) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: 'scan_in_log.rolls',
                    name: 'scan_in_log.rolls',
                    render: function(data, type, full, meta) {
                        return data ? data : '0';
                    }
                },
                {
                    data: 'scan_in_log.weight',
                    name: 'scan_in_log.weight',
                    render: function(data, type, full, meta) {
                        return data ? data : '-';
                    }
                },
                {
                    data: 'scan_in_log.yards',
                    name: 'scan_in_log.yards',
                    render: function(data, type, full, meta) {
                        return data ? data : '-';
                    }
                },
                {
                    data: 'scan_in_log.s_i_product_name.product_type',
                    name: 'scan_in_log.s_i_product_name.product_type',
                    render: function(data, type, full, meta) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: 'scan_in_log.s_i_color.name',
                    name: 'scan_in_log.s_i_color.name',
                    render: function(data, type, full, meta) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: 'scan_in_log.s_i_c_g_t.grade_name',
                    name: 'scan_in_log.s_i_c_g_t.grade_name',
                    render: function(data, type, full, meta) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: 'scan_in_log.s_i_n_t.grade_name',
                    name: 'scan_in_log.s_i_n_t.grade_name',
                    visible: is_nt_column_hide == 'yes' ? false : true,
                    render: function(data, type, full, meta) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: 'scan_out_inv_date',
                    name: 'scan_out_inventory.created_at',
                }
            ]
        });

        $('select[name=year],select[name=warehouse_id],select[name=customer_id],input[name=from_date],input[name=to_date],select[name=release_number],select[name=cgt],select[name=nt],select[name=color]')
            .change(function() {

                scan_out_logs_table.draw();

            })
    </script>
@endsection
