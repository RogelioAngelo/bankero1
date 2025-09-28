@extends('layouts.app')
@section('content')
    <main class="pt-90">
    <div class="page-header text-center" style="background-image: url('{{ asset('assets/images/page-header-bg.jpg') }}')">
            <div class="container">
                <h1 class="page-title">Checkout<span>Shop</span></h1>
            </div><!-- End .container -->
        </div><!-- End .page-header -->
        <nav aria-label="breadcrumb" class="breadcrumb-nav">
            <div class="container">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Shop</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Checkout</li>
                </ol>
            </div><!-- End .container -->
        </nav><!-- End .breadcrumb-nav -->
        <section class="shop-checkout container">
            {{-- <h2 class="page-title">Shipping and Checkout</h2>
            <div class="checkout-steps">
                <a href="{{ route('cart.index') }}" class="checkout-steps__item active">
                    <span class="checkout-steps__item-number">01</span>
                    <span class="checkout-steps__item-title">
                        <span>Shopping Bag</span>
                        <em>Manage Your Items List</em>
                    </span>
                </a>
                <a href="javascript:void(0)" class="checkout-steps__item active">
                    <span class="checkout-steps__item-number">02</span>
                    <span class="checkout-steps__item-title">
                        <span>Shipping and Checkout</span>
                        <em>Checkout Your Items List</em>
                    </span>
                </a>
                <a href="javascript:void(0)" class="checkout-steps__item">
                    <span class="checkout-steps__item-number">03</span>
                    <span class="checkout-steps__item-title">
                        <span>Confirmation</span>
                        <em>Review And Submit Your Order</em>
                    </span>
                </a>
            </div> --}}
            <form name="checkout-form" action="{{ route('cart.place.an.order') }}" method="POST">
                @csrf
                <div class="checkout-form">
                    <div class="billing-info__wrapper">
                        <div class="row">

                            <div class="col-6">
                                <h4>SHIPPING DETAILS</h4>
                            </div>


                            @if ($address)
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="my-account__address-list">
                                            <div class="my-account__address-list-item">
                                                <div class="my-account__address-item__details">

                                                    <p>{{ $address->name }}</p>
                                                    <p>{{ $address->address }}</p>
                                                    <p>{{ $address->landmark }}</p>
                                                    <p>{{ $address->city }}, {{ $address->state }}, {{ $address->country }},
                                                    </p>
                                                    <p>{{ $address->zip }}</p>
                                                    <br>
                                                    <p>{{ $address->phone }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="name">Full Name *</label>
                                                <input type="text" class="form-control" name="name" required=""
                                                    value="{{ old('name') }}">
                                                @error('name')
                                                    <span class="text-danger">{{ $message }} </span>
                                                @enderror
                                            </div><!-- End .col-md-6 -->

                                            <div class="col-sm-6">
                                                <label>Phone Number *</label>
                                                <input type="text" class="form-control" name="phone" required=""
                                                    value="{{ old('phone') }}">
                                                @error('phone')
                                                    <span class="text-danger">{{ $message }} </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-4">
                                                <label>Postcode / ZIP *</label>
                                                <input type="text" class="form-control" name="zip" required=""
                                                    value="{{ old('zip') }}">
                                                @error('zip')
                                                    <span class="text-danger">{{ $message }} </span>
                                                @enderror
                                            </div><!-- End .col-sm-4 -->

                                            <div class="col-sm-4">
                                                <label for="state">State / County *</label>
                                                <input type="text" class="form-control" name="state" required=""
                                                    value="{{ old('state') }}">
                                                @error('state')
                                                    <span class="text-danger">{{ $message }} </span>
                                                @enderror
                                            </div><!-- End .col-sm-4 -->

                                            <div class="col-sm-4">
                                                <label for="city">Town / City *</label>
                                                <input type="text" class="form-control" name="city" required=""
                                                    value="{{ old('city') }}">
                                                @error('city')
                                                    <span class="text-danger">{{ $message }} </span>
                                                @enderror
                                            </div><!-- End .col-sm-4 -->

                                        </div>

                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label for="address">House no, Building Name *</label>
                                                <input type="text" class="form-control" name="address" required=""
                                                    value="{{ old('address') }}">
                                                @error('address')
                                                    <span class="text-danger">{{ $message }} </span>
                                                @enderror
                                            </div>


                                            <div class="col-sm-6">
                                                <label for="locality">Road Name, Area, Colony *</label>
                                                <input type="text" class="form-control" name="locality" required=""
                                                    value="{{ old('locality') }}">
                                                @error('locality')
                                                    <span class="text-danger">{{ $message }} </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <label>Landmark *</label>
                                        <input type="text" class="form-control" name="landmark" required=""
                                            value="{{ old('landmark') }}">
                                        @error('landmark')
                                            <span class="text-danger">{{ $message }} </span>
                                        @enderror

                                    </div>

                                </div>
                            @endif
                            <aside class="col-lg-3">
                                <div class="summary">
                                    <h3 class="summary-title">Your Order</h3><!-- End .summary-title -->

                                    <table class="table table-summary">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>



                                        <tbody>
                                            {{-- Dynamic Cart Items --}}
                                            @foreach (Cart::instance('cart')->content() as $item)
                                                <tr>
                                                    <td>
                                                        {{ $item->name }} x {{ $item->qty }}
                                                    </td>
                                                    <td>
                                                        ₱{{ number_format($item->subtotal, 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach

                                            {{-- Totals Section --}}
                                            @if (Session::has('discounts'))
                                                <tr class="summary-subtotal">
                                                    <td>Subtotal:</td>
                                                    <td>₱{{ Cart::instance('cart')->subtotal() }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Discount ({{ Session::get('coupon')['code'] }})</td>
                                                    <td>-₱{{ Session::get('discounts')['discount'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Subtotal after discount:</td>
                                                    <td>₱{{ Session::get('discounts')['subtotal'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Shipping:</td>
                                                    <td>Free shipping</td>
                                                </tr>
                                                <tr>
                                                    <td>VAT:</td>
                                                    <td>₱{{ Session::get('discounts')['tax'] }}</td>
                                                </tr>
                                                <tr class="summary-total">
                                                    <td>Total:</td>
                                                    <td>₱{{ Session::get('discounts')['total'] }}</td>
                                                </tr>
                                            @else
                                                <tr class="summary-subtotal">
                                                    <td>Subtotal:</td>
                                                    <td>₱{{ Cart::instance('cart')->subtotal() }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Shipping:</td>
                                                    <td>Free shipping</td>
                                                </tr>
                                                <tr>
                                                    <td>VAT:</td>
                                                    <td>₱{{ Cart::instance('cart')->tax() }}</td>
                                                </tr>
                                                <tr class="summary-total">
                                                    <td>Total:</td>
                                                    <td>₱{{ Cart::instance('cart')->total() }}</td>
                                                </tr>
                                            @endif
                                        </tbody>


                                    </table>


                                    <div class="checkout__payment-methods" id="accordion-payment">

                                        <!-- Debit or Credit Card -->
                                        <div class="form-check d-flex align-items-center">
                                            <input class="form-check-input form-check-input_fill" type="radio" name="mode" id="mode1"
                                                value="card" data-toggle="collapse" data-target="#cardDetails"
                                                aria-expanded="false" aria-controls="cardDetails">
                                            <label class="form-check-label" for="mode1">
                                                Debit or Credit Card
                                            </label>
                                        </div>

                                        <div id="cardDetails" class="collapse" data-parent="#accordion-payment">
                                            <div class="card card-body">
                                                <p>Enter your debit or credit card details securely. <br>
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Cash on Delivery -->
                                        <div class="form-check d-flex align-items-center">
                                            <input class="form-check-input form-check-input_fill" type="radio"
                                                name="mode" id="mode2" value="cod" data-toggle="collapse"
                                                data-target="#codDetails" aria-expanded="false"
                                                aria-controls="codDetails">
                                            <label class="form-check-label" for="mode2">
                                                Cash on Delivery
                                            </label>
                                        </div>
                                        <div id="codDetails" class="collapse" data-parent="#accordion-payment">
                                            <div class="card card-body">
                                                <p>You can pay with cash when your order is delivered.</p>
                                            </div>
                                        </div>

                                        <!-- E-Wallet -->
                                        <div class="form-check d-flex align-items-center">
                                            <input class="form-check-input form-check-input_fill" type="radio"
                                                name="mode" id="mode3" value="e-wallet" data-toggle="collapse"
                                                data-target="#ewalletDetails" aria-expanded="false"
                                                aria-controls="ewalletDetails">
                                            <label class="form-check-label" for="mode3">
                                                E-Wallet
                                            </label>
                                        </div>
                                        <div id="ewalletDetails" class="collapse" data-parent="#accordion-payment">
                                            <div class="card card-body">
                                                <p>Supported wallets: GCash, PayMaya, GrabPay, etc.
                                                    <img src="{{ asset('new-assets/images/payments.png') }}" alt="Card Logos"
                                                        style="max-width:200px; margin-top: 10px;">
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Privacy -->
                                        <div class="policy-text mt-3">
                                            Your personal data will be used to process your order, support your experience
                                            throughout this website, and for other purposes described in our
                                            <a href="terms.html" target="_blank">privacy policy</a>.
                                        </div>

                                    </div>

                                    <style>
                                        .form-check-label {
                                            margin-left: 10px;
                                            /* adjust value as needed */
                                        }
                                    </style>

                                    <button type="submit" class="btn btn-outline-primary-2 btn-order btn-block">
                                        <span class="btn-text ">Place Order</span>
                                        <span class="btn-hover-text">Proceed to Checkout</span>
                                    </button>

                                </div><!-- End .summary -->
                            </aside>
                        </div>
                    </div>


                </div>
            </form>
        </section>
    </main>
@endsection
