@extends('admin.layout.app')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Cumulative NT Summary</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Cumulative NT Summary</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- Filter --}}
                        <form class="mb-5" id="submit_comulative" method="post"
                            action="{{ route('admin:inventory.getComulativeNtByFilter') }}">
                            @csrf
                            <div class="form-group row">

                                <div class="col-md-6">
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

                                <div class="col-md-6">
                                    <label class="mb-2" for="year">Year</label>
                                    <select class="form-control form-control-sm year select2" name="year">
                                        <option value=""> All </option>
                                        @foreach (getListOfYears() as $key => $year)
                                            <option value="{{ $year }}"> {{ $year }} </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mt-2">
                                    <label class="mb-2" for="From">From</label>
                                    <input type="Date" name="from_date" class=" form-control form-control-sm from mr-20"
                                        value="id=&quot;datepicker&quot;">
                                </div>
                                <div class="col-md-6 mt-2">
                                    <label class="mb-2" for="To">To</label>
                                    <input type="Date" name="to_date" class="form-control form-control-sm to"
                                        value="id=&quot;datepicker&quot;">
                                </div>
                                <div class="col-sm-12 mt-5">
                                    <button type="submit" class="btn btn-sm btn-primary"
                                        id="submit_comulative_summary">Submit</button>
                                </div>

                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
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
    </script>
@endsection
