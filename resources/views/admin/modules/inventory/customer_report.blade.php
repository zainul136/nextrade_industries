@extends('admin.layout.app')
@section('title', 'Customer Report')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Customer Report</li>
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
                        <form action="{{ route('admin:inventory.getCustomerReportByRlsNo') }}" method="POST">
                            @csrf
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="release_no">Release Number</label>
                                    @if (Session::has('release_number_error'))

                                      <input class="form-control form-control-sm" id="release_no" name="release_number" type="text" value="{{ Session::get('release_number_error')['release_number'] }}">
                                    @else
                                      <input class="form-control form-control-sm" id="release_no" name="release_number" type="text" value="{{ old('release_number') }}">

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
