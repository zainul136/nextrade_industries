@extends('admin.layout.app')
@section('title', 'Create Supplier')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin:suppliers') }}">Suppliers</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add Supplier</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">

                            <h4 class="card-title">Add Supplier</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin:suppliers.store') }}" method="POST">
                            @csrf
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="Full Name">Supplier Name</label>
                                    <input class="form-control form-control-sm" id="name" name="name" type="text"
                                        value="{{ old('name') }}" />
                                    @error('name')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="Contact">Contact</label>
                                    <input class="form-control form-control-sm" id="Contact" name="contact" type="number"
                                        value="{{ old('contact') }}" />
                                    @error('contact')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="email">Email</label>
                                    <input class="form-control form-control-sm" id="email" name="email" type="email"
                                        value="{{ old('email') }}" />
                                    @error('email')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="country">Country</label>
                                    <input class="form-control form-control-sm" id="country" name="country" type="text"
                                        value="{{ old('country') }}" />
                                    @error('country')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="product">Product</label>
                                    <input class="form-control form-control-sm" id="product" name="product" type="text"
                                        value="{{ old('product') }}" />
                                    @error('product')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="address">Address</label>
                                    <input class="form-control form-control-sm" id="address" name="address" type="text"
                                        value="{{ old('address') }}" />
                                    @error('address')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
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
