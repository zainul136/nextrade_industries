@extends('admin.layout.app')
@section('title', 'Edit Customer')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin:customers') }}">Customers</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Customer</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">

                            <h4 class="card-title">Edit Customer</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin:customers.update', encrypt($customer->id)) }}"
                            method="POST">
                            @csrf @method('PUT')
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="customerName">Customer Name</label>
                                    <input class="form-control form-control-sm" id="customerName" name="name"
                                        type="text" value="{{ old('name', $customer->name ?? '') }}" />
                                    @error('name')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="contact">Contact</label>
                                    <input type="text" class="form-control form-control-sm" id="contact" name="contact"
                                        value="{{ old('contact', $customer->contact ?? '') }}" />
                                    @error('contact')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mt-4">

                                <div class="col-md-6">
                                    <label for="email">Email</label>
                                    <input class="form-control form-control-sm" id="email" name="email" type="email"
                                        value="{{ old('email', $customer->email ?? '') }}" />
                                    @error('email')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="country">Country</label>
                                    <input class="form-control form-control-sm" id="country" name="country" type="text"
                                        value="{{ old('country', $customer->country ?? '') }}" />
                                    @error('country')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mt-4">
                                <div class="col-md-6">
                                    <label for="product">Product</label>
                                    <input class="form-control form-control-sm" id="product" name="product" type="text"
                                        value="{{ old('product', $customer->product ?? '') }}" />
                                    @error('product')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="address">Address</label>
                                    <input class="form-control form-control-sm" id="address" name="address" type="text"
                                        value="{{ old('address', $customer->address ?? '') }}">
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
