<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Bankero and Fishermen Association</title>
    <meta name="keywords" content="HTML5 Template">
    <meta name="description" content="Bankero and Fishermen Association">
    <meta name="author" content="p-themes">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('new-assets/images/icons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32"
        href="{{ asset('new-assets/images/icons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16"
        href="{{ asset('new-assets/images/icons/favicon-16x16.png') }}">
    <!-- FIXED: use a proper manifest if available (not site.html) -->
    {{-- <link rel="manifest" href="{{ asset('new-assets/images/icons/manifest.json') }}"> --}}
    <link rel="mask-icon" href="{{ asset('new-assets/images/icons/safari-pinned-tab.svg') }}" color="#666666">
    <link rel="shortcut icon" href="{{ asset('new-assets/images/icons/favicon.ico') }}">
    <meta name="apple-mobile-web-app-title" content="Bankero and Fishermen Association">
    <meta name="application-name" content="Bankero and Fishermen Association">
    <meta name="msapplication-TileColor" content="#cc9966">
    <meta name="msapplication-config" content="{{ asset('new-assets/images/icons/browserconfig.xml') }}">
    <meta name="theme-color" content="#ffffff">

    <!-- Vendor CSS -->
    <link rel="stylesheet"
        href="{{ asset('new-assets/vendor/line-awesome/line-awesome/line-awesome/css/line-awesome.min.css') }}">
    <link rel= "stylesheet"
        href= "https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <!-- Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('new-assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('new-assets/css/plugins/owl-carousel/owl.carousel.css') }}">
    <link rel="stylesheet" href="{{ asset('new-assets/css/plugins/magnific-popup/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('new-assets/css/plugins/jquery.countdown.css') }}">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('new-assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('new-assets/css/skins/skin-demo-6.css') }}">
    <link rel="stylesheet" href="{{ asset('new-assets/css/demos/demo-6.css') }}">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.css" rel="stylesheet">

    @stack('styles')

</head>


<body>
    <div class="page-wrapper">
        <header class="header header-6">
            <div class="header-top">
                <div class="container">
                    <div class="header-left">
                        <ul class="top-menu top-link-menu d-none d-md-block">
                            <li>
                                <a href="#">Links</a>
                                <ul>
                                    <li><a href="tel:#"><i class="icon-phone"></i>Call: +0123 456 789</a></li>
                                </ul>
                            </li>
                        </ul><!-- End .top-menu -->
                    </div><!-- End .header-left -->

                    <div class="header-right">
                        <div class="social-icons social-icons-color">
                            <a href="#" class="social-icon social-facebook" title="Facebook" target="_blank"><i
                                    class="icon-facebook-f"></i></a>
                            <a href="#" class="social-icon social-twitter" title="Twitter" target="_blank"><i
                                    class="icon-twitter"></i></a>
                            <a href="#" class="social-icon social-pinterest" title="Instagram" target="_blank"><i
                                    class="icon-pinterest-p"></i></a>
                            <a href="#" class="social-icon social-instagram" title="Pinterest" target="_blank"><i
                                    class="icon-instagram"></i></a>
                        </div><!-- End .soial-icons -->
                        @guest
                            <div class="top-menu top-link-menu">
                                <a href="{{ route('login') }}"><img
                                        src="https://bootdey.com/img/Content/avatar/avatar1.png"
                                        class="profile-pict-img img-fluid" alt=""
                                        style="width: 20px; height: 20px; margin-right: 5px; object-fit: cover;" />
                                    Sign in/Sign Up
                                </a>
                            </div>
                        @else
                            <div class="header-tools__item hover-container">
                                <a href="{{ Auth::user()->utype === 'ADM' ? route('admin.index') : route('user.index') }}"
                                    class="d-flex align-items-center gap-2">
                                    @if (Auth::user()->profile_photo)
                                        <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}"
                                            class="profile-pict-img" alt="Profile"
                                            style="width: 20px; height: 20px; object-fit: cover;" />
                                    @else
                                        <img src="https://bootdey.com/img/Content/avatar/avatar1.png"
                                            class="profile-pict-img" alt="Default"
                                            style="width: 20px; height: 20px; object-fit: cover;" />
                                    @endif
                                    <span>
                                        {{ Auth::user()->first_name }}
                                    </span>
                                </a>
                            </div>
                        @endguest

                        {{-- <div class="header-dropdown">
                            <a href="#">USD</a>
                            <div class="header-menu">
                                <ul>
                                    <li><a href="#">Eur</a></li>
                                    <li><a href="#">Usd</a></li>
                                </ul>
                            </div><!-- End .header-menu -->
                        </div><!-- End .header-dropdown -->

                        <div class="header-dropdown">
                            <a href="#">Eng</a>
                            <div class="header-menu">
                                <ul>
                                    <li><a href="#">English</a></li>
                                    <li><a href="#">French</a></li>
                                    <li><a href="#">Spanish</a></li>
                                </ul>
                            </div><!-- End .header-menu -->
                        </div><!-- End .header-dropdown --> --}}
                    </div><!-- End .header-right -->
                </div>
            </div>
            <div class="header-middle">
                <div class="container">
                    <div class="header-left">
                        <div
                            class="header-search header-search-extended header-search-visible d-none d-lg-block position-relative">
                            <form id="search-form" action="javascript:void(0);" method="get">
                                <div class="header-search-wrapper search-wrapper-wide">
                                    <label for="q" class="sr-only">Search</label>
                                    <button class="btn btn-primary" type="submit"><i
                                            class="icon-search"></i></button>
                                    <input type="search" class="form-control" name="q" id="search-input"
                                        placeholder="Search product ..." autocomplete="off">
                                </div>
                            </form>

                            <!-- 🔎 Modal Dropdown -->
                            <div id="search-results" class="position-absolute bg-white shadow rounded w-100 mt-2"
                                style="display:none; max-height: 300px; overflow-y:auto; z-index:999;">
                            </div>
                        </div>
                    </div>

                    <div class="header-center">
                        <a href="{{ route('home.index') }}" class="logo">
                            <img src="{{ asset('new-assets/images/logo.png') }}" alt="Bankero Association Logo"
                                width="120" height="20">
                        </a>
                    </div><!-- End .header-left -->

                    <div class="header-right">

                        <a href="{{ route('wishlist.index') }}" class=" wishlist-link">
                            <i class="icon-heart-o"></i>
                            <span class="wishlist-count">{{ Cart::instance('wishlist')->content()->count() }}</span>
                            <span class="wishlist-txt">My Wishlist</span>
                            {{-- @if (Cart::instance('wishlist')->content()->count() > 0)
                                <span
                                    class="cart-amount d-block position-absolute js-cart-items-count">{{ Cart::instance('wishlist')->content()->count() }}
                                </span>
                            @endif --}}
                        </a>



                        <div class="dropdown cart-dropdown">

                            <a href="{{ route('cart.index') }}" class="dropdown-toggle">
                                <i class="icon-shopping-cart"></i>
                                <span class="cart-count">{{ Cart::instance('cart')->content()->count() }}</span>
                                <span class="cart-txt">Cart</span>

                            </a>


                        </div><!-- End .cart-dropdown -->
                    </div>
                </div><!-- End .container -->
            </div><!-- End .header-middle -->

            <div class="header-bottom sticky-header">
                <div class="container">
                    <div class="header-left">
                        <nav class="main-nav">
                            <ul class="menu sf-arrows">
                                <li class="{{ request()->routeIs('home.index') ? 'active' : '' }}">
                                    <a href="{{ route('home.index') }}" class="navigation__link">Home</a>
                                </li>
                                <li class="{{ request()->routeIs('shop.index') ? 'active' : '' }}">
                                    <a href="{{ route('shop.index') }}" class="navigation__link">Shop</a>
                                </li>
                                <li class="{{ request()->routeIs('cart.index') ? 'active' : '' }}">
                                    <a href="{{ route('cart.index') }}" class="navigation__link">Cart</a>
                                </li>
                                <li class="{{ request()->routeIs('about') ? 'active' : '' }}">
                                    <a href="{{ route('about') }}" class="navigation__link">About</a>
                                </li>
                                <li class="{{ request()->routeIs('home.contact') ? 'active' : '' }}">
                                    <a href="{{ route('home.contact') }}" class="navigation__link">Contact</a>
                                </li>
                            </ul><!-- End .menu -->
                        </nav><!-- End .main-nav -->

                        <button class="mobile-menu-toggler">
                            <span class="sr-only">Toggle mobile menu</span>
                            <i class="icon-bars"></i>
                        </button>
                    </div><!-- End .header-left -->


                </div><!-- End .container -->
            </div><!-- End .header-bottom -->
        </header><!-- End .header -->

        {{-- main --}}
        @yield('content')
        {{-- end main --}}

        <footer class="footer">
            <div class="footer-middle">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-6 col-lg-3">
                            <div class="widget widget-about">
                                <h4 class="widget-title">about Bankero and Fishermen Association</h4>
                                <!-- End .widget-title -->
                                <p>Praesent dapibus, neque id cursus ucibus, tortor neque egestas augue, eu vulputate
                                    magna eros eu erat. </p>

                                <div class="social-icons">
                                    <a href="#" class="social-icon" title="Facebook" target="_blank"><i
                                            class="icon-facebook-f"></i></a>
                                    <a href="#" class="social-icon" title="Twitter" target="_blank"><i
                                            class="icon-twitter"></i></a>
                                    <a href="#" class="social-icon" title="Instagram" target="_blank"><i
                                            class="icon-instagram"></i></a>
                                    <a href="#" class="social-icon" title="Youtube" target="_blank"><i
                                            class="icon-youtube"></i></a>
                                </div><!-- End .soial-icons -->
                            </div><!-- End .widget about-widget -->
                        </div><!-- End .col-sm-6 col-lg-3 -->

                        <div class="col-sm-6 col-lg-3">
                            <div class="widget">
                                <h4 class="widget-title">Useful Links</h4><!-- End .widget-title -->

                                <ul class="widget-list">
                                    <li><a href="about.html">About Bankerobay</a></li>
                                    <li><a href="#">How to shop on Bankerobay</a></li>
                                    <li><a href="#">FAQ</a></li>
                                    <li><a href="contact.html">Contact us</a></li>
                                    <li><a href="login.html">Log in</a></li>
                                </ul><!-- End .widget-list -->
                            </div><!-- End .widget -->
                        </div><!-- End .col-sm-6 col-lg-3 -->

                        <div class="col-sm-6 col-lg-3">
                            <div class="widget">
                                <h4 class="widget-title">Customer Service</h4><!-- End .widget-title -->

                                <ul class="widget-list">
                                    <li><a href="#">Payment Methods</a></li>
                                    <li><a href="#">Money-back guarantee!</a></li>
                                    <li><a href="#">Returns</a></li>
                                    <li><a href="#">Shipping</a></li>
                                    <li><a href="#">Terms and conditions</a></li>
                                    <li><a href="#">Privacy Policy</a></li>
                                </ul><!-- End .widget-list -->
                            </div><!-- End .widget -->
                        </div><!-- End .col-sm-6 col-lg-3 -->

                        <div class="col-sm-6 col-lg-3">
                            <div class="widget">
                                <h4 class="widget-title">My Account</h4><!-- End .widget-title -->

                                <ul class="widget-list">
                                    <li><a href="#">Sign In</a></li>
                                    <li><a href="cart.html">View Cart</a></li>
                                    <li><a href="#">My Wishlist</a></li>
                                    <li><a href="#">Track My Order</a></li>
                                    <li><a href="#">Help</a></li>
                                </ul><!-- End .widget-list -->
                            </div><!-- End .widget -->
                        </div><!-- End .col-sm-6 col-lg-3 -->
                    </div><!-- End .row -->
                </div><!-- End .container -->
            </div><!-- End .footer-middle -->

            <div class="footer-bottom">
                <div class="container">
                    <figure class="footer-payments">
                        <img src="{{ asset('new-assets/images/payments.png') }}" alt="Payment methods" width="272"
                            height="20">
                    </figure><!-- End .footer-payments -->
                    <img src="{{ asset('new-assets/images/logo.png') }}" alt="Bankero Logo" width="82" height="25">
                    <p class="footer-copyright">Copyright © 2019 Store. All Rights Reserved.</p>
                    <!-- End .footer-copyright -->
                </div><!-- End .container -->
            </div><!-- End .footer-bottom -->
        </footer><!-- End .footer -->

    </div><!-- End .page-wrapper -->
    <button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>

    <!-- Mobile Menu -->
    <div class="mobile-menu-overlay"></div><!-- End .mobil-menu-overlay -->

    <div class="mobile-menu-container">
        <div class="mobile-menu-wrapper">
            <span class="mobile-menu-close"><i class="icon-close"></i></span>

            <form action="#" method="get" class="mobile-search">
                <label for="mobile-search" class="sr-only">Search</label>
                <input type="search" class="form-control" name="mobile-search" id="mobile-search"
                    placeholder="Search in..." required>
                <button class="btn btn-primary" type="submit"><i class="icon-search"></i></button>
            </form>

            <nav class="mobile-nav">
                <ul class="mobile-menu">
                    <li class="{{ request()->routeIs('home.index') ? 'active' : '' }}">
                        <a href="{{ route('home.index') }}" class="navigation__link">Home</a>
                    </li>
                    <li class="{{ request()->routeIs('shop.index') ? 'active' : '' }}">
                        <a href="{{ route('shop.index') }}" class="navigation__link">Shop</a>
                    </li>
                    <li class="{{ request()->routeIs('cart.index') ? 'active' : '' }}">
                        <a href="{{ route('cart.index') }}" class="navigation__link">Cart</a>
                    </li>
                    <li class="{{ request()->routeIs('about') ? 'active' : '' }}">
                        <a href="{{ route('about') }}" class="navigation__link">About</a>
                    </li>
                    <li class="{{ request()->routeIs('home.contact') ? 'active' : '' }}">
                        <a href="{{ route('home.contact') }}" class="navigation__link">Contact</a>
                    </li>


                </ul>
            </nav><!-- End .mobile-nav -->

            <div class="social-icons">
                <a href="#" class="social-icon" target="_blank" title="Facebook"><i
                        class="icon-facebook-f"></i></a>
                <a href="#" class="social-icon" target="_blank" title="Twitter"><i
                        class="icon-twitter"></i></a>
                <a href="#" class="social-icon" target="_blank" title="Instagram"><i
                        class="icon-instagram"></i></a>
                <a href="#" class="social-icon" target="_blank" title="Youtube"><i
                        class="icon-youtube"></i></a>
            </div><!-- End .social-icons -->
        </div><!-- End .mobile-menu-wrapper -->
    </div><!-- End .mobile-menu-container -->

    <!-- Sign In / Register Modal -->
    {{-- <div class="modal fade" id="signin-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="icon-close"></i></span>
                    </button>

                    <div class="form-box">
                        <div class="form-tab">
                            <ul class="nav nav-pills nav-fill" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="signin-tab" data-toggle="tab" href="#signin"
                                        role="tab" aria-controls="signin" aria-selected="true">Sign In</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="register-tab" data-toggle="tab" href="#register"
                                        role="tab" aria-controls="register" aria-selected="false">Register</a>
                                </li>
                            </ul>

                            <div class="tab-content" id="tab-content-5">
                                <!-- Sign In Tab -->
                                <div class="tab-pane fade show active" id="signin" role="tabpanel"
                                    aria-labelledby="signin-tab">
                                    <form method="POST" action="{{ route('login') }}">
                                        @csrf
                                        <div class="form-group">
                                            <label for="signin-email">Email *</label>
                                            <input type="email"
                                                class="form-control form-control_gray @error('email') is-invalid @enderror"
                                                id="signin-email" name="email" value="{{ old('email') }}"
                                                required autocomplete="email"autofocus="">
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
                                                id="signin-password" name="password" required
                                                autocomplete="current-password">
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
                                                <input type="checkbox" class="custom-control-input"
                                                    id="signin-remember">
                                                <label class="custom-control-label" for="signin-remember">Remember
                                                    Me</label>
                                            </div><!-- End .custom-checkbox -->

                                            <a href="#" class="forgot-link">Forgot Your Password?</a>
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
                                </div>

                                <!-- Register Tab -->
                                <div class="tab-pane fade" id="register" role="tabpanel"
                                    aria-labelledby="register-tab">
                                    <form method="POST" action="{{ route('register') }}">
                                        @csrf
                                        <div class="form-group">
                                            <label for="register-email">Name *</label>
                                            <input type="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                id="register-name" name="name" value="{{ old('name') }}"
                                                autocomplete="name" autofocus required>
                                            @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="register-email">Email *</label>
                                            <input type="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                id="email " name="email" value="{{ old('email') }}" required
                                                autocomplete="email">
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="register-email">Phone Number *</label>
                                            <input type="text"
                                                class="form-control @error('mobile') is-invalid @enderror"
                                                id="email " name="mobile" value="{{ old('mobile') }}" required
                                                value="{{ old('mobile') }}" autocomplete="mobile">
                                            @error('mobile')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>


                                        <div class="form-group">
                                            <label for="register-password">Password *</label>
                                            <input type="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                id="password" name="password" required autocomplete="new-password">
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="register-password">Confirm Password *</label>
                                            <input type="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                id="password_confirm" name="password_confirmation" required
                                                autocomplete="new-password">
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
                                                <input type="checkbox" class="custom-control-input"
                                                    id="register-policy" required>
                                                <label class="custom-control-label" for="register-policy">I agree to
                                                    the <a href="#">privacy policy</a> *</label>
                                            </div><!-- End .custom-checkbox -->
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
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div><!-- End .form-box -->
                </div><!-- End .modal-body -->
            </div><!-- End .modal-content -->
        </div><!-- End .modal-dialog -->
    </div> --}}


    <!-- Plugins JS File -->
    <script src="{{ asset('new-assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('new-assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('new-assets/js/jquery.hoverIntent.min.js') }}"></script>
    <script src="{{ asset('new-assets/js/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('new-assets/js/superfish.min.js') }}"></script>
    <script src="{{ asset('new-assets/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('new-assets/js/bootstrap-input-spinner.js') }}"></script>
    <script src="{{ asset('new-assets/js/jquery.plugin.min.js') }}"></script>
    <script src="{{ asset('new-assets/js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('new-assets/js/jquery.countdown.min.js') }}"></script>
    <!-- Main JS File -->
    <script src="{{ asset('new-assets/js/main.js') }}"></script>
    <script src="{{ asset('new-assets/js/demos/demo-6.js') }}"></script>

    <script src="{{ asset('assets/js/plugins/bootstrap.bundle.min.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.js"></script>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    @stack('scripts')

    <script>
        (function () {
            // Persist last visited navigation link or active tab.
            const KEY = 'bankerobay:lastPath';

            // Save clicks on main navigation links
            document.addEventListener('click', function (e) {
                const a = e.target.closest && e.target.closest('a.navigation__link');
                if (a && a.href) {
                    try { localStorage.setItem(KEY, a.getAttribute('href')); } catch (err) {}
                }
            }, true);

            // Also save clicks on mobile nav
            document.addEventListener('click', function (e) {
                const a = e.target.closest && e.target.closest('.mobile-menu .navigation__link');
                if (a && a.href) {
                    try { localStorage.setItem(KEY, a.getAttribute('href')); } catch (err) {}
                }
            }, true);

            // Restore on load: if current location is root or different from saved, navigate there via AJAX fetch
            document.addEventListener('DOMContentLoaded', function () {
                let saved = null;
                try { saved = localStorage.getItem(KEY); } catch (err) {}
                if (!saved) return;

                const current = window.location.pathname + window.location.search;
                // Only auto-restore when user is at root or index, or when explicit query param ?restore=1 is present
                const shouldRestore = current === '/' || current === '/home' || new URLSearchParams(window.location.search).has('restore');
                if (!shouldRestore) return;

                // Fetch saved path and replace content via AJAX into body main area
                fetch(saved, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(resp => {
                        if (!resp.ok) throw new Error('Network');
                        return resp.text();
                    })
                    .then(html => {
                        // Try to extract the main content from returned HTML (yielded by server)
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newMain = doc.querySelector('body').innerHTML;
                        // Replace body content while preserving scripts and top-level layout where possible
                        document.body.innerHTML = newMain;
                        // Update the URL without reloading
                        window.history.replaceState({}, '', saved);
                    })
                    .catch(() => {
                        // fallback: navigate normally
                        window.location.href = saved;
                    });
            });

            // Persist tab state for bootstrap tabs (store by tab container id)
            document.addEventListener('click', function (e) {
                const tabLink = e.target.closest && e.target.closest('[data-toggle="tab"]');
                if (!tabLink) return;
                try {
                    const href = tabLink.getAttribute('href');
                    const container = tabLink.closest('.tab-content') || document;
                    const key = 'bankerobay:tab:' + (container.id || 'default');
                    localStorage.setItem(key, href);
                } catch (err) {}
            }, true);

            // Restore tabs on load
            try {
                document.querySelectorAll('.tab-content').forEach(function (container) {
                    const key = 'bankerobay:tab:' + (container.id || 'default');
                    const savedTab = localStorage.getItem(key);
                    if (savedTab) {
                        const link = document.querySelector('[data-toggle="tab"][href="' + savedTab + '"]');
                        if (link) {
                            // Use bootstrap's tab show if available, otherwise toggle classes
                            if (typeof jQuery !== 'undefined' && jQuery(link).tab) {
                                jQuery(link).tab('show');
                            } else {
                                document.querySelectorAll(container.querySelectorAll('.tab-pane')).forEach(function (p) { p.classList.remove('show','active'); });
                                const pane = document.querySelector(savedTab);
                                pane && pane.classList.add('show','active');
                            }
                        }
                    }
                });
            } catch (err) {}
        })();
    </script>
</body>




</html>
