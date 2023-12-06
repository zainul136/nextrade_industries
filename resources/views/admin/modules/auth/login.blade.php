@extends('admin.layout.auth')
@section('authContent')
    <section class="login-content">
        <div class="row m-0 align-items-center bg-white vh-100">
            <div class="col-md-8">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <div class="card card-transparent shadow-none d-flex justify-content-center mb-0 auth-card">
                            <div class="card-body">
                                <a href="{{ env('APP_URL') }}" class="navbar-brand d-flex align-items-center mb-3">
                                    <!--Logo start-->
                                    <img src="{{ asset('assets/images/logo/Nextlogo.png') }}" alt="Nextrade Logo"
                                        style="width: 350px; height: 100px; padding: 10px; margin-left: 15px;">
                                    <!--logo End-->
                                </a>
                                <p class="text-center">Login to stay connected.</p>
                                @if (session()->has('error'))
                                    <div class="alert alert-danger" role="alert">
                                        {{ session()->get('error') }}
                                    </div>
                                @endif
                                <form action="{{ route('admin:login.action') }}" method="POST">
                                    @csrf

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group {{ $errors->has('email') ? ' has-danger' : '' }}">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email"
                                                    class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}"
                                                    id="email" value="{{ old('email') }}" aria-describedby="email"
                                                    placeholder=" " name="email" required>
                                                @if ($errors->has('email'))
                                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                                        <strong>{{ $errors->first('email') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group {{ $errors->has('password') ? ' has-danger' : '' }}">
                                                <label for="password" class="form-label">Password</label>
                                                <input type="password"
                                                    class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}"
                                                    id="password" name="password" aria-describedby="password" placeholder=" ">
                                                @if ($errors->has('password'))
                                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                                        <strong>{{ $errors->first('password') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-12 d-flex justify-content-between">
                                            <div class="form-check mb-3">
                                                <input type="checkbox" class="form-check-input" id="customCheck1">
                                                <label class="form-check-label" for="customCheck1">Remember Me</label>
                                            </div>
                                            <a href="{{route('admin:forget_password')}}">Forgot Password?</a>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center p-4">
                                        <button type="submit" class="btn btn-primary">Sign In</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sign-bg">
                    <img src="{{asset('assets/images/logo/Nextlogo.png')}}" alt="Nextrade Logo" style="opacity:0.05">
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
