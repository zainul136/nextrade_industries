@extends('admin.layout.app')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Inventory Summary</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Inventory Summary</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- Filter --}}
                        <form class="mb-5" id="filter" method="post" action="#">
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <label class="mb-2" for="Warehouses">Warehouses</label>
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
                                <div class="col-md-3">
                                    <label class="mb-2" for="Year">Year</label>
                                    <select class="form-control form-control-sm year select2" name="year">
                                        <option value=""> All Time</option>
                                        <option value="current" selected> Current</option>
                                        @foreach (getListOfYears() as $key => $year)
                                            <option value="{{ $year }}"> {{ $year }} </option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="col-md-3">
                                    <label class="mb-2" for="From">From</label>
                                    <input type="Date" name="from_date" class="form-control form-control-sm from mr-20"
                                        value="id=&quot;datepicker&quot;">
                                </div>
                                <div class="col-md-3">
                                    <label class="mb-2" for="To">To</label>
                                    <input type="Date" name="to_date" class="form-control form-control-sm to"
                                        value="id=&quot;datepicker&quot;">
                                </div>

                                <div class="col-md-3 mt-2">

                                    <label class="mb-2" for="type">Type</label><br />
                                    <input type="radio" name="inv_type" value="weight" id="type_weight"checked>
                                    <label for="type_weight">Weight</label>

                                    <input type="radio" name="inv_type" value="yards" id="type_yard">
                                    <label for="type_yard">Yards</label>

                                </div>
                            </div>
                        </form>
                        <div class="flex-wrap d-flex align-items-center justify-content-between">
                            <div class="col-lg-12 col-md-12">
                                <div class="row" id="inventory_summary_cols_">

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
        getInventorySummary()

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
        $('select[name=year],select[name=warehouse_id],input[name=from_date],input[name=to_date],input[name=inv_type]')
            .change(function() {

                getInventorySummary()

            })


        function getInventorySummary() {

            $.ajax({
                type: 'POST',
                url: "{{ route('admin:inventory.getInventoryByFilter') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    warehouse_id: $('select[name=warehouse_id] option:selected').val(),
                    year: $('select[name=year] option:selected').val(),
                    from_date: $('input[name=from_date]').val(),
                    to_date: $('input[name=to_date]').val(),
                    inv_type: $('input[name=inv_type]:checked').val()
                },
                success: function(response) {

                    if (response.status === true) {

                        $('#inventory_summary_cols_').html(response.data)

                    }
                }
            });

        }
    </script>
@endsection
