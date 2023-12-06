@extends('admin.layout.app')
@section('content')
    <?php
    if (isset($user_data->id) && $user_data->id != 0) {
        $submit_url = route('admin:user.update', [$user_data->id ?? '']);
    } else {
        $submit_url = route('admin:user.add');
    }
    ?>
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin:users') }}">Users</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            @if (isset($user_data->id) && $user_data->id != 0)
                                Edit User
                            @else
                                Add User
                            @endif
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">
                                @if (isset($user_data->id) && $user_data->id != 0)
                                    Edit User
                                @else
                                    Add User
                                @endif
                            </h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ $submit_url }}">
                            @csrf
                            <div class="form-group row">
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
                                    <input class="form-control form-control-sm" id="Contact" name="contact" type="number"
                                        value="{{ old('contact', $user_data->contact ?? '') }}">
                                    @if ($errors->has('contact'))
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $errors->first('contact') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row mt-4">
                                <div class="col-md-6">
                                    <label for="Email">Email</label>
                                    <input class="form-control form-control-sm" id="Email" name="email" type="email"
                                        value="{{ old('email', $user_data->email ?? '') }}">
                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label for="Address">Address</label>
                                    <input class="form-control form-control-sm" id="Address" name="address" type="text"
                                        value="{{ old('address', $user_data->address ?? '') }}">
                                    @if ($errors->has('address'))
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $errors->first('address') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row mt-4">
                                @if (!isset($user_data->id))
                                    <div class="col-md-6">
                                        <label for="password">Password</label>
                                        <input class="form-control form-control-sm" id="password" name="password"
                                            type="password" value={{ old('password', '') }}>
                                        @if ($errors->has('password'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                <div class="col-md-6">
                                    <label for="Email">Role</label>
                                    <select class="form-control form-control-sm" name="role">
                                        @if (isset($roles) & !empty($roles))
                                            @foreach ($roles as $key => $role)
                                                <option value={{ $role->id }}
                                                    {{ isset($user_data->role) && $user_data->role == $role->id ? 'selected' : '' }}>
                                                    {{ $role->name ?? 'N/A' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4 d-flex justify-content-start">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    @if (isset($user_data->id) && $user_data->id != 0)
                                        Submit
                                    @else
                                        Add
                                    @endif
                                </button>
                            </div>
                        </form>
                        <br><br>
                        @if (isset($user_data) && !empty($user_data))
                            <div class="header-title mb-4">
                                <h4 class="card-title">
                                    Change Password
                                </h4>
                            </div>
                            @if (auth()->user()->id == 1)
                                <form method="post" action="{{ route('admin:user.admin_change_password') }}">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $user_data->id ?? '' }}">
                                    <div class="form-group row">
                                        @if ($errors->has('errors'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('errors') }}</strong>
                                            </span>
                                        @endif
                                        <br><br>
                                        <div class="col-md-6">
                                            <label for="new_password">New Password</label>
                                            <input class="form-control form-control-sm" id="new_password"
                                                name="new_password" type="text" value="{{ old('new_password') }}">
                                            @if ($errors->has('new_password'))
                                                <span class="invalid-feedback" style="display: block;" role="alert">
                                                    <strong>{{ $errors->first('new_password') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <label for="confirm_password">Confirm Password</label>
                                            <input class="form-control form-control-sm" id="confirm_password"
                                                name="confirm_password" type="text"
                                                value="{{ old('confirm_password') }}">
                                            @if ($errors->has('confirm_password'))
                                                <span class="invalid-feedback" style="display: block;" role="alert">
                                                    <strong>{{ $errors->first('confirm_password') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-4 d-flex justify-content-start">
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            Submit
                                        </button>
                                    </div>
                                </form>
                            @else
                                <form method="post" action="{{ route('admin:user.change_password') }}">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $user_data->id ?? '' }}">
                                    <div class="form-group row">
                                        @if ($errors->has('errors'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('errors') }}</strong>
                                            </span>
                                        @endif
                                        <br><br>

                                        <div class="col-md-6">
                                            <label for="current_password">Current Password</label>
                                            <input class="form-control form-control-sm" id="current_password"
                                                name="current_password" type="text"
                                                value="{{ old('current_password') }}">
                                            @if ($errors->has('current_password'))
                                                <span class="invalid-feedback" style="display: block;" role="alert">
                                                    <strong>{{ $errors->first('current_password') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <label for="new_password">New Password</label>
                                            <input class="form-control form-control-sm" id="new_password"
                                                name="new_password" type="text" value="{{ old('new_password') }}">
                                            @if ($errors->has('new_password'))
                                                <span class="invalid-feedback" style="display: block;" role="alert">
                                                    <strong>{{ $errors->first('new_password') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="col-md-6 mt-4">
                                            <label for="confirm_password">Confirm Password</label>
                                            <input class="form-control form-control-sm" id="confirm_password"
                                                name="confirm_password" type="text"
                                                value="{{ old('confirm_password') }}">
                                            @if ($errors->has('confirm_password'))
                                                <span class="invalid-feedback" style="display: block;" role="alert">
                                                    <strong>{{ $errors->first('confirm_password') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-4 d-flex justify-content-start">
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            @if (isset($user_data->id) && $user_data->id != 0)
                                                Submit
                                            @else
                                                Add
                                            @endif
                                        </button>
                                    </div>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
