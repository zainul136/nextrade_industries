@extends('admin.layout.app')
@section('title', 'Edit CGT Grade')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin:cgt_grades') }}">CGT Grades</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit CGT Grade</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">

                            <h4 class="card-title">Edit CGT Grade</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin:cgt_grades.update', encrypt($cgtGrade->id)) }}" method="POST">
                            @csrf @method('Put')
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="grade_name">Grade Name</label>
                                    <input class="form-control form-control-sm" id="grade_name" name="grade_name"
                                        type="text" value="{{ old('grade_name', $cgtGrade->grade_name ?? '') }}" />
                                    @error('grade_name')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="slug">Slug</label>
                                    <input class="form-control form-control-sm" id="slug" name="slug" type="text"
                                        value="{{ old('slug', $cgtGrade->slug ?? '') }}" />
                                    @error('slug')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-4">
                                    <label for="price">Price</label>
                                    <input class="form-control form-control-sm" id="price" name="price" type="number"
                                        step="any" value="{{ old('price', $cgtGrade->price ?? '') }}" />
                                    @error('price')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-4">
                                    <label for="billing_code">Billing Code</label>
                                    <input class="form-control form-control-sm" id="billing_code" name="billing_code"
                                        type="text" value="{{ old('billing_code', $cgtGrade->billing_code ?? '') }}" />
                                    @error('billing_code')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                {{-- <div class="col-md-6 mt-4">
                                    <label for="pnl">PNL</label>
                                    <input class="form-control form-control-sm" id="pnl" name="pnl"
                                        type="text" value="{{ old('pnl', $cgtGrade->pnl ?? '') }}" />
                                    @error('pnl')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div> --}}
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
