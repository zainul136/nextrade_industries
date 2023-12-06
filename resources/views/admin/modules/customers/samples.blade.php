@extends('admin.layout.app')
@section('title', 'Customer Samples')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin:customers') }}">Customers</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Customer Samples</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Customer Samples</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="d-flex justify-content-between mt-3 mb-3">
                                <div class="header-title">
                                    <h5 class="card-title">
                                        Customer Basic Information
                                    </h5>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="Full Name">Full Name : <span class="text-dark">
                                        {{ $customer->name ?? '' }}</span></label>
                            </div>
                            <div class="col-md-6">
                                <label for="Contact">Contact : <span class="text-dark">
                                        {{ $customer->contact ?? '' }}</span></label>
                            </div>
                            <div class="col-md-6 mt-4">
                                <label for="Email">Email : <span class="text-dark">
                                        {{ $customer->email ?? '' }}</span></label>
                            </div>
                            <div class="col-md-6 mt-4">
                                <label for="Country">Country : <span class="text-dark">
                                        {{ $customer->country ?? '' }}</span></label>
                            </div>
                            <div class="col-md-6 mt-4">
                                <label for="Product">Product : <span class="text-dark">
                                        {{ $customer->product ?? '' }}</span></label>
                            </div>
                            <div class="col-md-6 mt-4">
                                <label for="Address">Address : <span class="text-dark">
                                        {{ $customer->address ?? '' }}</span></label>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="d-flex justify-content-between mt-3 mb-3">
                                <div class="header-title">
                                    <h5 class="card-title">
                                        Samples
                                    </h5>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="sample-list" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @isset($samples)
                                            @foreach ($samples as $key => $sample)
                                                <tr>
                                                    <td>{{ $key + 1 ?? '-' }}</td>
                                                    <td>{{ $sample->type ?? '-' }}</td>
                                                    <td>{{ $sample->status ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        @endisset
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
