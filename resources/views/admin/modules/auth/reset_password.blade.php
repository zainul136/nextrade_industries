@extends('admin.layout.auth')
@section('authContent')
    <section class="login-content">
        <div class="row m-0 align-items-center bg-white vh-100">
            <div class="col-md-8">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <div class="card card-transparent auth-card shadow-none d-flex justify-content-center mb-0">
                            <div class="card-body">
                                <a href="{{ env('APP_URL') }}" class="navbar-brand d-flex align-items-center mb-3">

                                    <!--Logo start-->

                                    <img src="{{ asset('assets/images/logo/Nextlogo.png') }}" alt="Nextrade Logo"
                                        style="width: 350px; height: 100px; padding: 10px; margin-left: 15px;">

                                    <!--logo End-->
                                    <h4 class="logo-title"></h4>

                                </a>
                                <h2 class="mb-2">Reset Password</h2>
                                <p>Reset Your password</p>
                                <form action="{{ route('password.update') }}" method="post">
                                    @csrf
                                    <div class="row">
                                        <input type="hidden" name="token" value={{ $token }}>
                                        <div class="col-lg-12">
                                            <div class="floating-label form-group">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email"
                                                    class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}"
                                                    id="email" value="{{ $email }}" aria-describedby="email"
                                                    placeholder=" " name="email" required>
                                                @if ($errors->has('email'))
                                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                                        <strong>{{ $errors->first('email') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="floating-label form-group">
                                                <label for="password " class="form-label">Password</label>
                                                <input type="password"
                                                    class="form-control {{ $errors->has('password ') ? ' is-invalid' : '' }}"
                                                    id="password " value="{{ old('password ') }}"
                                                    aria-describedby="password " placeholder=" " name="password " required>
                                                @if ($errors->has('password '))
                                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                                        <strong>{{ $errors->first('password ') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="floating-label form-group">
                                                <label for="password_confirmation " class="form-label">Confirm
                                                    Password</label>
                                                <input type="password"
                                                    class="form-control {{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}"
                                                    id="password_confirmation " value="{{ old('password_confirmation ') }}"
                                                    aria-describedby="email" placeholder=" " name="password_confirmation"
                                                    required>
                                                @if ($errors->has('password_confirmation'))
                                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Reset</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sign-bg">
                    <img src="{{ asset('assets/images/logo/Nextlogo.png') }}" alt="Nextrade Logo" style="opacity:0.05">
                    </svg>
                </div>
            </div>
            <div class="col-md-4 d-md-block d-none bg-primary p-0 mt-n1 vh-100 overflow-hidden">
                <img src="{{ asset('assets') }}/images/auth/01.png" class="img-fluid gradient-main animated-scaleX"
                    alt="images">
            </div>
        </div>
    </section>
@endsection
