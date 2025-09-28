@extends('layouts.app')

@section('content')
    <section class="mb-6">
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
                                    aria-controls="signin" aria-selected="true">Sign In</a>
                            </ul>
                        </div>
                        <form method="POST" action="{{ route('login') }} " name="login-form" class="needs-validation"
                            novalidate="">
                            @csrf
                            <div class="form-group">
                                <label for="signin-email">Email *</label>
                                <input type="email"
                                    class="form-control form-control_gray @error('email') is-invalid @enderror"
                                    id="signin-email" name="email" value="{{ old('email') }}" required
                                    autocomplete="email"autofocus="">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>


                            <div class="form-group">
                                <label for="signin-password">Password *</label>
                                <input type="password"
                                    class="form-control form-control_gray @error('password') is-invalid @enderror"
                                    id="signin-password" name="password" required autocomplete="current-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>


                            <div class="form-footer">
                                <button type="submit" class="btn btn-outline-primary-2">
                                    <span>LOG IN</span>
                                    <i class="icon-long-arrow-right"></i>
                                </button>

                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="signin-remember">
                                    <label class="custom-control-label" for="signin-remember">Remember
                                        Me</label>
                                </div><!-- End .custom-checkbox -->

                                <a href="{{ route('password.request') }}" class="forgot-link">Forgot Your Password?</a>
                            </div>
                        </form>
                        <div class="form-choice">
                            <p class="text-center">or sign in with</p>
                            <div class="row">
                                <div class="col-sm-6">
                                    <a href="#" class="btn btn-login btn-g">
                                        <i class="icon-google"></i>
                                        Login With Google
                                    </a>
                                </div><!-- End .col-6 -->
                                <div class="col-sm-6">
                                    <a href="#" class="btn btn-login btn-f">
                                        <i class="icon-facebook-f"></i>
                                        Login With Facebook
                                    </a>
                                </div><!-- End .col-6 -->
                            </div><!-- End .row -->
                        </div>
                        <div class="customer-option mt-2 text-center">
                            <span class="text-secondary">No account yet?</span>
                            <a href="{{ route('register') }}" class="btn-text js-show-register">Create Account</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
