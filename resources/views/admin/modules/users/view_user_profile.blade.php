@extends('admin.layout.app')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            View Profile
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">
                                View Profile
                            </h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-2 ">
                                <img class="profile-image"
                                    src="{{ auth()->user()->profile_picture ? asset('storage/images/profilePicture/' . auth()->user()->profile_picture) : asset('assets/images/evidence_demo.png') }}" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="d-flex justify-content-between mt-3 mb-3">
                                <div class="header-title">
                                    <h5 class="card-title">
                                        Basic Information
                                    </h5>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="Full Name">Full Name : <span class="text-dark">
                                        {{ $user_data->full_name ?? '' }}</span></label>
                            </div>
                            <div class="col-md-4">
                                <label for="Contact">Contact : <span class="text-dark">
                                        {{ $user_data->contact ?? '' }}</span></label>
                            </div>
                            <div class="col-md-4">
                                <label for="Email">Email : <span class="text-dark">
                                        {{ $user_data->email ?? '' }}</span></label>
                            </div>
                            <div class="col-md-4 mt-4">
                                <label for="Address">Address : <span class="text-dark">
                                        {{ $user_data->address ?? '' }}</span></label>
                            </div>
                            <div class="col-md-4  mt-4">
                                <label for="Address">Role : <span class="badge rounded-pill bg-success"> {{ $user_data->userRole->name ?? 'N/A' }} </span></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
