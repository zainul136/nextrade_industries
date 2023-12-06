@extends('admin.layout.app')
@section('title', 'Edit NT Grade')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin:nt_grades') }}">NT Grades</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit NT Grade</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">

                            <h4 class="card-title">Edit NT Grade</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin:nt_grades.update', encrypt($ntGrade->id)) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="grade_name">Grade Name</label>
                                    <input class="form-control form-control-sm" id="grade_name" name="grade_name"
                                        type="text" value="{{ old('grade_name', $ntGrade->grade_name ?? '') }}">
                                    @error('grade_name')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="slug">Slug</label>
                                    <input class="form-control form-control-sm" id="slug" name="slug" type="text"
                                        value="{{ old('slug', $ntGrade->slug ?? '') }}">
                                    @error('slug')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-3 d-flex justify-content-start">
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
