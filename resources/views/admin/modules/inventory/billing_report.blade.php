@extends('admin.layout.app')
@section('title', 'Billing Report')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Billing Report</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">

                            <h4 class="card-title">Search Reference Number</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin:inventory.getBillingReportByRefNo') }}" method="POST">
                            @csrf
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label class="mb-2" for="referece_no">Reference Number</label>
                                    @if (Session::has('reference_number_error'))
                                        <input class="form-control form-control-sm" id="referece_no" name="reference_number"
                                            type="text"
                                            value="{{ Session::get('reference_number_error')['reference_number'] }}">
                                    @else
                                        <input class="form-control form-control-sm" id="referece_no" name="reference_number"
                                            type="text" value="{{ old('reference_number') }}">
                                    @endif
                                    @if ($errors->has('reference_number'))
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $errors->first('reference_number') }}</strong>
                                        </span>
                                    @endif

                                    @if (Session::has('reference_number_error'))
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ Session::get('reference_number_error')['msg'] }}</strong>
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
        $('input[name=reference_number]').keyup(function() {
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
                $('input[name=reference_number]').val('').prop('disabled', true);
            } else {
                $('input[name=reference_number]').prop('disabled', false);
            }
        });
    </script>
@endsection
