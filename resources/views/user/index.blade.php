@extends('layouts.app')
@section('content')
    <main class="pt-90">

        <nav aria-label="breadcrumb" class="breadcrumb-nav mb-3">
            <div class="container">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Shop</a></li>
                    <li class="breadcrumb-item active" aria-current="page">My Account</li>
                </ol>
            </div><!-- End .container -->
        </nav>
        <section class="my-account container">

            <h2 class="page-title">My Account</h2>
            <div class="container py-5">
                <div class="row">
                    <div class="col-lg-3">
                        @include('user.account-nav')
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card mb-4">
                                <div class="card-body text-center">
                                    @if (Auth::user()->profile_photo)
                                        <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" alt="avatar"
                                            class="rounded-circle img-fluid d-block mx-auto"
                                            style="width: 150px; height: 150px; object-fit: cover; overflow: hidden;" />
                                    @else
                                        <img src="https://bootdey.com/img/Content/avatar/avatar1.png" alt="avatar"
                                            class="rounded-circle img-fluid d-block mx-auto"
                                            style="width: 150px; height: 150px; object-fit: cover; overflow: hidden;" />
                                    @endif
                                    <h5 class="my-3">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h5>
                                </div>

                            </div>
                            {{-- <div class="card mb-4 mb-lg-0">
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush rounded-3">
                                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                            <i class="fas fa-globe fa-lg text-warning"></i>
                                            <p class="mb-0">https://mdbootstrap.com</p>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                            <i class="fab fa-github fa-lg text-body"></i>
                                            <p class="mb-0">mdbootstrap</p>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                            <i class="fab fa-twitter fa-lg" style="color: #55acee;"></i>
                                            <p class="mb-0">@mdbootstrap</p>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                            <i class="fab fa-instagram fa-lg" style="color: #ac2bac;"></i>
                                            <p class="mb-0">mdbootstrap</p>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                            <i class="fab fa-facebook-f fa-lg" style="color: #3b5998;"></i>
                                            <p class="mb-0">mdbootstrap</p>
                                        </li>
                                    </ul>
                                </div>
                            </div> --}}
                        </div>


                        <div class="col-lg-8">
                            <div class="row mb-3">
                                <div class="col-md-6 offset-md-6">
                                    <div class="card text-white bg-primary">
                                        <div class="card-body">
                                            <h6 class="card-title">Orders Summary</h6>
                                            <p class="mb-0">Total Orders: <strong>{{ $orders_count ?? 0 }}</strong></p>
                                            <p class="mb-0">Total Spent: <strong>₱{{ number_format($total_spent ?? 0, 2) }}</strong></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <p class="mb-0">Full Name</p>
                                        </div>
                                        <div class="col-sm-9">
                                            <p class="text-muted mb-0">{{ Auth::user()->first_name }}
                                                {{ Auth::user()->last_name }}
                                            </p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <p class="mb-0">Email</p>
                                        </div>
                                        <div class="col-sm-9">
                                            <p class="text-muted mb-0">{{ Auth::user()->email }}</p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <p class="mb-0">Phone</p>
                                        </div>
                                        <div class="col-sm-9">
                                            <p class="text-muted mb-0">{{ Auth::user()->mobile ?? 'Not set' }}</p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <p class="mb-0">Verify</p>
                                        </div>
                                        <div class="col-sm-9">
                                            @if (Auth::user()->hasVerifiedEmail())
                                                <div class="alert" role="alert">
                                                    Your email has been verified.
                                                </div>
                                            @else
                                                @if (session('resent'))
                                                    <div class="alert" role="alert">
                                                        A fresh verification link has been sent to your email address.
                                                    </div>
                                                @endif

                                                Before proceeding, please check your email for a verification link.
                                                If you did not receive the email,

                                                <form class="d-inline" method="POST"
                                                    action="{{ route('verification.resend') }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-link p-0 m-0 align-baseline">
                                                        click here to request another
                                                    </button>.
                                                </form>
                                            @endif


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
