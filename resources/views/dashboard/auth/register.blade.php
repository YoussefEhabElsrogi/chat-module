@extends('partials.dashboard.auth.auth')

@section('title', 'Register')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row"></div>
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
                                        <span>Create an Account</span>
                                    </h6>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <form class="form-horizontal" action="{{ route('dashboard.register') }}"
                                            method="POST" novalidate>
                                            @csrf

                                            <!-- Name -->
                                            <fieldset class="form-group position-relative has-icon-left">
                                                <input name="name" type="text" class="form-control input-lg" id="name"
                                                    value="{{ old('name') }}" placeholder="Enter your name" tabindex="1">
                                                @error('name')
                                                    <strong class="text-danger" style="float:left; margin-top:10px;">
                                                        {{ $message }}
                                                    </strong>
                                                @enderror
                                                <div class="form-control-position">
                                                    <i class="ft-user"></i>
                                                </div>
                                            </fieldset>

                                            <!-- Email -->
                                            <fieldset class="form-group position-relative has-icon-left">
                                                <input name="email" type="email" class="form-control input-lg" id="email"
                                                    value="{{ old('email') }}" placeholder="Enter your email" tabindex="2">
                                                @error('email')
                                                    <strong class="text-danger" style="float:left; margin-top:10px;">
                                                        {{ $message }}
                                                    </strong>
                                                @enderror
                                                <div class="form-control-position">
                                                    <i class="ft-mail"></i>
                                                </div>
                                            </fieldset>

                                            <!-- Password -->
                                            <fieldset class="form-group position-relative has-icon-left">
                                                <input name="password" type="password" class="form-control input-lg"
                                                    id="password" placeholder="Enter your password" tabindex="3">
                                                @error('password')
                                                    <strong class="text-danger" style="float:left; margin-top:10px;">
                                                        {{ $message }}
                                                    </strong>
                                                @enderror
                                                <div class="form-control-position">
                                                    <i class="la la-key"></i>
                                                </div>
                                            </fieldset>

                                            <!-- Confirm Password -->
                                            <fieldset class="form-group position-relative has-icon-left">
                                                <input name="password_confirmation" type="password"
                                                    class="form-control input-lg" id="password_confirmation"
                                                    placeholder="Confirm your password" tabindex="4">
                                                <div class="form-control-position">
                                                    <i class="la la-key"></i>
                                                </div>
                                            </fieldset>

                                            <!-- Submit -->
                                            <button type="submit" class="btn btn-success btn-block btn-lg">
                                                <i class="ft-user-plus"></i> Register
                                            </button>
                                        </form>
                                    </div>
                                    <div class="card-footer text-center">
                                        <p>Already have an account?
                                            <a href="{{ route('dashboard.login') }}">Login here</a>
                                        </p>
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
