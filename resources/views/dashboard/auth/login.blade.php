@extends('partials.dashboard.auth.auth')

@section('title', 'Login')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <section class="flexbox-container">
                    <div class="col-12 d-flex align-items-center justify-content-center">
                        <div class="col-md-4 col-10 box-shadow-2 p-0">
                            <div class="card border-grey border-lighten-3 m-0">
                                <div class="card-header border-0">
                                    <div class="card-title text-center">
                                        <img src="{{ asset('assets/dashboard/images/logo/logo-dark.png')}}"
                                            alt="branding logo">
                                    </div>
                                    <h6 class="card-subtitle line-on-side text-muted text-center font-small-3 pt-2">
                                        <span>Login</span>
                                    </h6>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <form class="form-horizontal" action="{{ route('dashboard.login') }}" method="POST"
                                            novalidate>
                                            @csrf
                                            <fieldset class="form-group position-relative has-icon-left">
                                                <input name="email" type="text" class="form-control input-lg"
                                                    value="{{ old('email') }}" id="email" placeholder="Enter your email"
                                                    tabindex="1">
                                                @error('email')
                                                    <strong class="text-danger" style="float:left; margin-top:10px;">
                                                        {{ $message }}
                                                    </strong>
                                                @enderror

                                                <div class="form-control-position">
                                                    <i class="ft-user"></i>
                                                </div>
                                            </fieldset>

                                            <fieldset class="form-group position-relative has-icon-left">
                                                <input name="password" type="password" class="form-control input-lg"
                                                    id="password" placeholder="Enter your password" tabindex="2">
                                                <div class="form-control-position">
                                                    <i class="la la-key"></i>
                                                </div>
                                                @error('password')
                                                    <strong class="text-danger" style="float:left; margin-top:10px;">
                                                        {{ $message }}
                                                    </strong>
                                                @enderror
                                            </fieldset>

                                            <!-- NoCaptcha fieldset -->
                                            <fieldset class="form-group" style="margin-bottom: 1rem;">
                                                {{-- {!! NoCaptcha::display() !!} --}}
                                                @error('g-recaptcha-response')
                                                    <strong class="text-danger" style="float:left; margin-top:0.5rem;">
                                                        {{ $message }}
                                                    </strong>
                                                @enderror
                                            </fieldset>

                                            <div class="form-group row">
                                                <div class="col-md-6 col-12 text-center text-md-left">
                                                    <fieldset>
                                                        <input name="remember" type="checkbox" id="remember-me"
                                                            class="chk-remember">
                                                        <label for="remember-me">Remember Me</label>
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-6 col-12 text-center text-md-right">
                                                    <a href="{{ route('dashboard.register') }}" class="card-link">
                                                        Make an account?
                                                    </a>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-danger btn-block btn-lg">
                                                <i class="ft-unlock"></i> Login
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
