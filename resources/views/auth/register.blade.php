@extends('layouts.app')

@section('content')
    <section class="mb-2">
        <div class="container-fluid h-custom">
            <div class="row d-flex justify-content-center align-items-center h-100 mt-2">
                <div class="col-md-9 col-lg-6 col-xl-5">
                    <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.webp"
                        class="img-fluid" alt="Sample image">
                </div>
                <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                    <div class="form-tab">
                        <div class="pb-2">
                            <ul class="nav nav-pills nav-fill" role="tablist">
                                <a class="nav-link active" id="signin-tab" data-toggle="tab" href="#signin" role="tab"
                                    aria-controls="signin" aria-selected="true">Register</a>
                            </ul>
                        </div>
                        <form method="POST" action="{{ route('register') }}" name="register-form" class="needs-validation"
                            novalidate="">
                            @csrf
                            <div class="form-group">
                                <label for="register-name">Name *</label>
                                <div class="row">
                                    {{-- First Name --}}
                                    <div class="col-md-6">
                                        <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                            id="register-first-name" name="first_name" value="{{ old('first_name') }}"
                                            placeholder="First Name" required>
                                        @error('first_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    {{-- Last Name --}}
                                    <div class="col-md-6">
                                        <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                            id="register-last-name" name="last_name" value="{{ old('last_name') }}"
                                            placeholder="Last Name" required>
                                        @error('last_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>



                            <div class="form-group">
                                <label for="register-email">Email *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email " name="email" value="{{ old('email') }}" required autocomplete="email">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="register-email">Phone Number *</label>
                                <input type="text" class="form-control @error('mobile') is-invalid @enderror"
                                    id="mobile " name="mobile" value="{{ old('mobile') }}" required
                                    value="{{ old('mobile') }}" autocomplete="mobile">
                                @error('mobile')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="register-password">Password *</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" required autocomplete="new-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="register-password">Confirm Password *</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password_confirm" name="password_confirmation" required autocomplete="new-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-footer">
                                <button type="submit" class="btn btn-outline-primary-2">
                                    <span>SIGN UP</span>
                                    <i class="icon-long-arrow-right"></i>
                                </button>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="register-policy" required>
                                    <label class="custom-control-label" for="register-policy">I agree to
                                        the <a href="#">privacy policy</a> *</label>
                                </div>
                            </div>
                            <div class="form-choice">
                                <p class="text-center">or Register with</p>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <a href="#" class="btn btn-login btn-g">
                                            <i class="icon-google"></i>
                                            Login With Google
                                        </a>
                                    </div><!-- End .col-6 -->
                                    <div class="col-sm-6">
                                        <a href="#" class="btn btn-login  btn-f">
                                            <i class="icon-facebook-f"></i>
                                            Login With Facebook
                                        </a>
                                    </div><!-- End .col-6 -->
                                </div><!-- End .row -->
                            </div>
                            <div class="customer-option mt-4 text-center">
                                <span class="text-secondary">Have an account?</span>
                                <a href="{{ route('login') }}" class="btn-text js-show-register">Login
                                    to your
                                    Account</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
