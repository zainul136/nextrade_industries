@extends('admin.layout.app')
@section('title', 'Create Product Type')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin:product.types') }}">Product Types</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add Product Type</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">

                            <h4 class="card-title">Add Product Type</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin:product.types.store') }}" method="POST">
                            @csrf
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="product_type">Product Type</label>
                                    <input class="form-control form-control-sm" id="product_type" name="product_type"
                                        type="text" value="{{ old('product_type') }}" />
                                    @error('product_type')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="slug">Slug</label>
                                    <input class="form-control form-control-sm" id="slug" name="slug" type="text"
                                        value="{{ old('slug') }}" />
                                    @error('slug')
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
