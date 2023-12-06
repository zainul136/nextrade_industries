@extends('admin.layout.app')
@section('title', 'Create Warehouse')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin:warehouses') }}">Warehouse</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add Warehouse</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">

                            <h4 class="card-title">Add Warehouse</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('admin:warehouses.store') }}">
                            @csrf
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="Full Name">Warehouse Name</label>
                                    <input class="form-control form-control-sm" id="name" name="name" type="text"
                                        value="{{ old('name') }}">
                                    @error('name')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="Contact">Contact</label>
                                    <input class="form-control form-control-sm" id="Contact" name="contact" type="text"
                                        value="{{ old('contact') }}">
                                    @error('contact')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mt-4">
                                <div class="col-md-6">
                                    <label for="location">Location</label>
                                    <input class="form-control form-control-sm" id="location" name="location"
                                        type="text" value="{{ old('location') }}">
                                    @error('location')
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
