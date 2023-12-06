@extends('admin.layout.app')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Edit Profile
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">
                                Edit Profile
                            </h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('admin:update-profile', [$user_data->id ?? '']) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="form-group row">
                                <div class="col-md-2">
                                    <img class="profile-image" id="preview_image"
                                        src="{{ auth()->user()->profile_picture ? asset('storage/images/profilePicture/' . auth()->user()->profile_picture) : asset('assets/images/evidence_demo.png') }}" />
                                    <input accept="image/*" class="d-none" name="profile_picture" type='file'
                                        id="uploaded_img" />
                                    <div class="text-center mt-4">
                                        <button type="button" class="btn btn-sm btn-primary upload_button"> Upload
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="Full Name">Full Name</label>
                                            <input class="form-control form-control-sm" id="Full Name" name="full_name"
                                                type="text" value="{{ old('full_name', $user_data->full_name ?? '') }}">
                                            @if ($errors->has('full_name'))
                                                <span class="invalid-feedback" style="display: block;" role="alert">
                                                    <strong>{{ $errors->first('full_name') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <label for="Contact">Contact</label>
                                            <input class="form-control form-control-sm" id="Contact" name="contact"
                                                type="number" value="{{ old('contact', $user_data->contact ?? '') }}">
                                            @if ($errors->has('contact'))
                                                <span class="invalid-feedback" style="display: block;" role="alert">
                                                    <strong>{{ $errors->first('contact') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="col-md-6 mt-4">
                                            <label for="Email">Email</label>
                                            <input class="form-control form-control-sm" id="Email" name="email"
                                                type="email" value="{{ old('email', $user_data->email ?? '') }}">
                                            @if ($errors->has('email'))
                                                <span class="invalid-feedback" style="display: block;" role="alert">
                                                    <strong>{{ $errors->first('email') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="col-md-6  mt-4">
                                            <label for="Address">Address</label>
                                            <input class="form-control form-control-sm" id="Address" name="address"
                                                type="text" value="{{ old('address', $user_data->address ?? '') }}">
                                            @if ($errors->has('address'))
                                                <span class="invalid-feedback" style="display: block;" role="alert">
                                                    <strong>{{ $errors->first('address') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                        {{-- <div class="col-md-6  mt-4">
                                            <label for="password">Password</label>
                                            <input class="form-control form-control-sm" id="password" name="password"
                                                type="password" value={{ old('password', '') }}>
                                            @if ($errors->has('password'))
                                                <span class="invalid-feedback" style="display: block;" role="alert">
                                                    <strong>{{ $errors->first('password') }}</strong>
                                                </span>
                                            @endif
                                        </div> --}}
                                        <div class="mt-4 d-flex justify-content-start">
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                Update profile
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function() {

            $(document).on('click', '.upload_button', function() {
                $("input[id='uploaded_img']").click();
            });

            $(document).on('change', '#uploaded_img', function() {
                const [file] = uploaded_img.files
                if (file) {
                    preview_image.src = URL.createObjectURL(file)
                }
            })
        });
    </script>
@endsection
