@extends('admin.layout.app')
@section('title', 'PNL Report')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">PNL Report</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">

                            <h4 class="card-title">Search Release Number</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin:inventory.getPnlReportByRlsNo') }}" method="POST">
                            @csrf
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <label class="mb-2" for="referece_no">Release Number</label>
                                    @if (Session::has('release_number_error'))
                                        <input class="form-control form-control-sm" id="release_no" name="release_number"
                                            type="text"
                                            value="{{ Session::get('release_number_error')['release_number'] }}">
                                    @else
                                        <input class="form-control form-control-sm" id="release_no" name="release_number"
                                            type="text" value="{{ old('release_number') }}">
                                    @endif
                                    @if ($errors->has('release_number'))
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $errors->first('release_number') }}</strong>
                                        </span>
                                    @endif

                                    @if (Session::has('release_number_error'))
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ Session::get('release_number_error')['msg'] }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="col-md-3">
                                    <label class="mb-2" for="From">From</label>
                                    <input type="Date" name="from_date" class=" form-control form-control-sm from mr-20"
                                        value="id=&quot;datepicker&quot;">
                                </div>
                                <div class="col-md-3">
                                    <label class="mb-2" for="To">To</label>
                                    <input type="Date" name="to_date" class="form-control form-control-sm to"
                                        value="id=&quot;datepicker&quot;">
                                </div>
                                <div class="col-md-3">
                                    <label class="mb-2" for="customer">Customers</label>
                                    <select class="form-control form-control-sm customers select2" name="customer_id">
                                        <option value=""> All </option>
                                        @if (isset($customers) & !empty($customers))
                                            @foreach ($customers as $key => $value)
                                                <option value="{{ $value->id }}">
                                                    {{ $value->name ?? '' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-6 float-right mt-4">
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        Submit
                                    </button>
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
        // check year is all or not
        $('input[name=release_number]').keyup(function() {
            let val = $(this).val();
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
                $('input[name=release_number]').val('').prop('disabled', true);
            } else {
                $('input[name=release_number]').prop('disabled', false);
            }
        });
    </script>
@endsection
