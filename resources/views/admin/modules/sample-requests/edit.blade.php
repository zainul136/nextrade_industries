@extends('admin.layout.app')
@section('title', 'Edit Sample Request')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin:sampleRequests') }}">Sample Requests</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Sample Request</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">

                            <h4 class="card-title">Edit Sample Request</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin:sampleRequests.update', $sample->id) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="customerName">Customer Name</label>
                                    <select class="form-control form-control-sm selected_customer select2"
                                        name="customer_id">
                                        <option value="">Select</option>
                                        @if (isset($sample->customer_id) & !empty($sample->customer_id))
                                            @foreach ($customers as $key => $value)
                                                <option value="{{ $value->id }}"
                                                    {{ $value->id == $sample->customer_id ? 'selected' : '' }}>
                                                    {{ ucfirst($value->name) ?? 'N/A' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('customer_id')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="type">Type</label>
                                    <input type="text" class="form-control form-control-sm" id="type" name="type"
                                        value="{{ $sample->type ?? '' }}" />
                                    @error('type')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mt-4">

                                <div class="col-md-6">
                                    <label for="email">Status</label>
                                    <select class="form-control form-control-sm selected_customer select2" name="status">
                                        <option value="requested"
                                            {{ isset($sample->status) && $sample->status == 'requested' ? 'selected' : '' }}>
                                            Requested</option>
                                        <option value="paid"
                                            {{ isset($sample->status) && $sample->status == 'paid' ? 'selected' : '' }}>
                                            Paid</option>
                                        <option value="approved"
                                            {{ isset($sample->status) && $sample->status == 'approved' ? 'selected' : '' }}>
                                            Approved</option>
                                        <option value="shipped"
                                            {{ isset($sample->status) && $sample->status == 'shipped' ? 'selected' : '' }}>
                                            Shipped</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-4 d-flex justify-content-start">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
