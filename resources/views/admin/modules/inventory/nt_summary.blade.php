@extends('admin.layout.app')
<style>
    thead,
    tbody,
    tfoot,
    tr,
    td,
    th {
        border-color: white !important;
        /* border-style: solid; */
        border-width: 0;
    }
</style>
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">NT Summary</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card" style="height: 100vh">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">NT Summary</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="mb-5" id="filter" method="post" action="#">
                            <input type="hidden" name="_token" value="o7N65XSj76NzYlItILoVBbGyzhU2x7BKm0QtrrxL">
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <label class="mb-2" for="NT">NT Grade</label>
                                    <select class="form-control form-control-sm nt_id select2" name="nt_id">
                                        <option value=""> All </option>
                                        @if (isset($nt) & !empty($nt))
                                            @foreach ($nt as $key => $value)
                                                <option value="{{ $value->id }}">
                                                    {{ $value->grade_name ?? '' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="mb-2" for="cgt">CGT Grade</label>
                                    <select class="form-control form-control-sm cgt_id select2" name="cgt_id">
                                        <option value=""> All </option>
                                        @if (isset($cgt) & !empty($cgt))
                                            @foreach ($cgt as $key => $value)
                                                <option value="{{ $value->id }}">
                                                    {{ $value->grade_name ?? '' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="mb-2" for="Product_type">Product Type</label>
                                    <select class="form-control form-control-sm nt_id select2" name="product_type_id">
                                        <option value=""> All </option>
                                        @if (isset($product_type) & !empty($product_type))
                                            @foreach ($product_type as $key => $value)
                                                <option value="{{ $value->id }}">
                                                    {{ $value->product_type ?? '' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="mb-2" for="warehouse">Warehouses</label>
                                    <select class="form-control form-control-sm warehouses select2" name="warehouse_id">
                                        <option value=""> All </option>
                                        @if (isset($warehouses) & !empty($warehouses))
                                            @foreach ($warehouses as $key => $value)
                                                <option value="{{ $value->id }}">
                                                    {{ $value->name ?? '' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="col-md-3 mt-2">
                                    <label class="mb-2" for="year">Year</label>
                                    <select class="form-control form-control-sm year select2" name="year">
                                        <option value=""> All </option>
                                        <option value="current" selected> Current</option>
                                        @foreach (getListOfYears() as $key => $year)
                                            <option value="{{ $year }}"> {{ $year }} </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3 mt-2">
                                    <label class="mb-2" for="From">From</label>
                                    <input type="Date" name="from_date" class=" form-control form-control-sm from mr-20"
                                        value="id=&quot;datepicker&quot;">
                                </div>
                                <div class="col-md-3 mt-2">
                                    <label class="mb-2" for="To">To</label>
                                    <input type="Date" name="to_date" class="form-control form-control-sm to"
                                        value="id=&quot;datepicker&quot;">
                                </div>
                            </div>
                        </form>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="flex-wrap d-flex justify-content-between align-items-center">
                                    <div class="header-title">
                                        <h4 class="card-title" id="nt_total_val">{{ $total_scan_in_weight ?? '' }}</h4>
                                        <p class="mb-0">Grand Total</p>
                                    </div>
                                    <div class="d-flex align-items-center align-self-center">
                                        <div class="d-flex align-items-center text-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" viewBox="0 0 24 24"
                                                fill="currentColor">
                                                <g>
                                                    <circle cx="12" cy="12" r="8"
                                                        fill="currentColor">
                                                    </circle>
                                                </g>
                                            </svg>
                                            <div class="ms-2">
                                                <span class="text-secondary">NT Grade</span>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center ms-3 text-info">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" viewBox="0 0 24 24"
                                                fill="currentColor">
                                                <g>
                                                    <circle cx="12" cy="12" r="8"
                                                        fill="currentColor">
                                                    </circle>
                                                </g>
                                            </svg>
                                            <div class="ms-2">
                                                <span class="text-secondary">CGT Grade</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-wrap d-flex align-items-center justify-content-between"
                                    style="margin-top: 70px; margin-left: 140px;">
                                    <div id="myChart" class="col-md-6 myChart"></div>
                                    <div class="d-grid gap col-md-6">
                                        <div class="d-flex align-items-start">
                                            <svg class="mt-2" xmlns="http://www.w3.org/2000/svg" width="14"
                                                viewBox="0 0 24 24" fill="#3a57e8">
                                                <g>
                                                    <circle cx="12" cy="12" r="8" fill="#3a57e8">
                                                    </circle>
                                                </g>
                                            </svg>
                                            <div class="ms-3">
                                                <span class="text-secondary" id="nt_slug">NT Grade</span>
                                                <li class="mt-1" id="nt_slug_val">0</li>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <svg class="mt-2" xmlns="http://www.w3.org/2000/svg" width="14"
                                                viewBox="0 0 24 24" fill="#4bc7d2">
                                                <g>
                                                    <circle cx="12" cy="12" r="8" fill="#4bc7d2">
                                                    </circle>
                                                </g>
                                            </svg>
                                            <div class="ms-3">
                                                <span class="text-secondary" id="cgt_slug">CGT Grade</span>

                                                <li class="mt-1" id="cgt_slug_val">0</li>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- <div id="chart-1"></div> --}}
                            </div>
                            <div class="col-md-6">
                                <div class="table-responsive mt-4" style="overflow-y:auto; max-height:50vh">
                                    <table id="cgt-colors" class="table table-bordered mb-0 colors_table" role="grid">
                                        <thead>
                                            <tr>
                                                <th>NT</th>
                                                <th>CGT</th>
                                                <th>Color</th>
                                                <th>Weight</th>
                                                <th>Yards</th>
                                            </tr>
                                        </thead>
                                        <tbody id="colors_rows_">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        // get cgt summary
        getNTSummary()

        // check year is all or not
        $('select[name=year]').change(function() {
            let val = $(this, 'option:selected').val()

            if (val != '') {

                $('input[name=from_date],input[name=to_date]').val('').prop('disabled', true)

            } else {

                $('input[name=from_date],input[name=to_date]').val('').prop('disabled', false)

            }

        })

        $('input[name=from_date], input[name=to_date]').change(function() {
            let fromDateVal = $('input[name=from_date]').val();
            let toDateVal = $('input[name=to_date]').val();

            if (fromDateVal !== '' || toDateVal !== '') {
                $('select[name=year]').val('').prop('disabled', true);
            } else {
                $('select[name=year]').prop('disabled', false);
            }
        });
        // any field change
        $('select[name=cgt_id],select[name=nt_id],select[name=year],select[name=warehouse_id],select[name=product_type_id],input[name=from_date],input[name=to_date]')
            .change(function() {

                getNTSummary()

            })

        function getNTSummary() {

            $.ajax({
                type: 'POST',
                url: "{{ route('admin:inventory.getNtSummaryByFilter') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    cgt_id: $('select[name=cgt_id] option:selected').val(),
                    nt_id: $('select[name=nt_id] option:selected').val(),
                    warehouse_id: $('select[name=warehouse_id] option:selected').val(),
                    product_type_id: $('select[name=product_type_id] option:selected').val(),
                    year: $('select[name=year] option:selected').val(),
                    from_date: $('input[name=from_date]').val(),
                    to_date: $('input[name=to_date]').val()
                },
                success: function(response) {

                    if (response.status === true) {

                        let data = response.result

                        $('#cgt_slug').html(data.cgt_slug);
                        $('#cgt_slug_val').html(data.total_weight_cgt_wise);
                        $('#nt_slug').html(data.nt_slug);
                        $('#nt_slug_val').html(data.total_weight_nt_wise);
                        $('#nt_total_val').html(data.total_weight_nt_wise);
                        $('#colors_rows_').html(data.colors_weight_html);

                    }
                }
            });

        }
    </script>
@endsection
