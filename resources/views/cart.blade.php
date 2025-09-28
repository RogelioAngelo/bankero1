@extends('layouts.app')

@section('content')
    <style>
        .text-success {
            color: #278c04 !important;
        }
    </style>

    <main class="pt-90">
        <div class="page-header text-center" style="background-image: url('assets/images/page-header-bg.jpg')">
            <div class="container">
                <h1 class="page-title">Shopping Cart<span>Shop</span></h1>
            </div><!-- End .container -->
        </div>
        <nav aria-label="breadcrumb" class="breadcrumb-nav">
            <div class="container">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Shop</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Shopping Cart</li>
                </ol>
            </div><!-- End .container -->
        </nav>

        <div class="mb-1 pb-1"></div>
        <section class="shop-checkout container">


            {{-- <div class="checkout-steps mb-2">
                <a href="{{ route('cart.index') }}" class="checkout-steps__item active">
                    <span class="checkout-steps__item-number">01</span>
                    <span class="checkout-steps__item-title">
                        <span>Shopping Bag</span>
                        <em>Manage Your Items List</em>
                    </span>
                </a>
                <a href="{{ route('cart.checkout') }}" class="checkout-steps__item">
                    <span class="checkout-steps__item-number">02</span>
                    <span class="checkout-steps__item-title">
                        <span>Shipping and Checkout</span>
                        <em>Checkout Your Items List</em>
                    </span>
                </a>
                <a href="#" class="checkout-steps__item">
                    <span class="checkout-steps__item-number">03</span>
                    <span class="checkout-steps__item-title">
                        <span>Confirmation</span>
                        <em>Review And Submit Your Order</em>
                    </span>
                </a>
            </div> --}}








            <div class="page-content">
                <div class="cart">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-9">
                                @if ($items->count() > 0)
                                    <table class="table table-cart table-mobile">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Price</th>
                                                <th>Quantity</th>
                                                <th>Total</th>
                                                <th></th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($items as $item)
                                                <tr>
                                                    <td class="product-col">
                                                        <div class="product">
                                                            <figure class="product-media">
                                                                <a href="#">
                                                                    <img src="{{ asset('uploads/products/thumbnails/' . ($item->model->image ?? 'default.png')) }}"
                                                                        alt="Product image">
                                                                </a>
                                                            </figure>

                                                            <h3 class="product-title">
                                                                <a href="#">
                                                                    <h5>{{ $item->name }}</h5>
                                                                </a>
                                                            </h3><!-- End .product-title -->
                                                        </div>

                                                    </td>
                                                    <td class="price-col">₱{{ $item->price }}</td>
                                                    <td>
                                                        <div class="qty-control position-relative">
                                                            <form method="POST"
                                                                action="{{ route('cart.update', ['rowId' => $item->rowId]) }}"
                                                                class="qty-control d-flex align-items-center">
                                                                @csrf
                                                                @method('PUT')

                                                                <div class="cart-product-quantity">
                                                                    <!-- Decrease -->
                                                                    <button type="submit" name="action" value="decrease"
                                                                        class="qty-control__reduce">-</button>

                                                                    <!-- Quantity -->
                                                                    <span class="qty-number">{{ $item->qty }}</span>

                                                                    <!-- Increase -->
                                                                    <button type="submit" name="action" value="increase"
                                                                        class="qty-control__increase">+</button>
                                                                </div>
                                                            </form>
                                                        </div>

                                                        <style>
                                                            .cart-product-quantity {
                                                                display: flex;
                                                                align-items: center;
                                                                justify-content: center;
                                                                gap: 15px;
                                                                border: 1px solid #ddd;

                                                                padding: 5px 12px;
                                                                min-width: 100px;
                                                                text-align: center;
                                                            }

                                                            .qty-number {
                                                                font-size: 16px;
                                                                font-weight: 500;
                                                                color: #333;
                                                                min-width: 20px;
                                                                text-align: center;
                                                            }

                                                            .qty-control__reduce,
                                                            .qty-control__increase {
                                                                background: none;
                                                                border: none;
                                                                padding: 0;
                                                                cursor: pointer;
                                                                font-size: 20px;

                                                                color: #333;
                                                                transition: color 0.2s;
                                                            }

                                                            .qty-control__reduce:hover,
                                                            .qty-control__increase:hover {
                                                                color: #000;
                                                            }
                                                        </style>

                                                    </td>
                                                    <td class="total-col">₱{{ $item->subtotal }}</td>
                                                    <td class="remove-col">
                                                        <form method="POST"
                                                            action="{{ route('cart.item.remove', $item->rowId) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn-remove"><i
                                                                    class="icon-close"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    {{-- <div class="checkout-discount">
                                        <form action="#">
                                            <input type="text" class="form-control" required
                                                id="checkout-discount-input">
                                            <label for="checkout-discount-input" class="text-truncate discount-label">
                                                Have a coupon? <span>Click here to enter your code</span>
                                            </label>
                                        </form>
                                    </div> --}}

                                    <div class="checkout-discount">
                                        @if (!Session::has('coupon'))
                                            {{-- Apply Coupon --}}
                                            <form action="{{ route('cart.coupon.apply') }}" method="POST">
                                                @csrf
                                                <input type="text" class="form-control" name="coupon_code" required
                                                    id="checkout-discount-input">
                                                <label for="checkout-discount-input" class="text-truncate discount-label">
                                                    Have a coupon? <span>Click here to enter your code</span>
                                                </label>
                                            </form>
                                        @else
                                            {{-- Remove Coupon --}}
                                            <form action="{{ route('cart.coupon.remove') }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <div class="input-group">
                                                    <input class="form-control" type="text"
                                                        value="{{ Session::get('coupon')['code'] }} Applied!" readonly>


                                                        <button class="btn btn-remove-code" type="submit">
                                                            <i class="icon-close"></i> Remove
                                                        </button>

                                                </div>

                                            </form>
                                        @endif
                                    </div>


                                    @if (Session::has('success'))
                                        <div class="text-success mt-2">{{ Session::get('success') }}</div>
                                    @elseif (Session::has('error'))
                                        <div class="text-danger mt-2">{{ Session::get('error') }}</div>
                                    @endif
                                @else
                                    <div class="row">
                                        <div class="col-md-12 d-flex justify-content-center">
                                            <div class="text-center py-5" style="max-width: 400px; width: 100%;">
                                                <p>No item's found in your cart</p>
                                                <a href="{{ route('shop.index') }}"
                                                    class="btn btn-outline-primary-2 btn-order w-10">
                                                    Shop Now
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <aside class="col-lg-3">
                                <div class="summary summary-cart">
                                    <h3 class="summary-title">Cart Total</h3>

                                    <table class="table table-summary">
                                        <tbody>
                                            <tr class="summary-subtotal">
                                                <td>Subtotal:</td>
                                                <td>₱{{ Cart::instance('cart')->subtotal() }}</td>
                                            </tr>
                                            @if (Session::has('discounts'))
                                                <tr>
                                                    <td>Discount ({{ Session::get('coupon')['code'] }}):</td>
                                                    <td>₱{{ Session::get('discounts')['discount'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Subtotal after discount:</td>
                                                    <td>₱{{ Session::get('discounts')['subtotal'] }}</td>
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
                                                <tr>
                                                    <td>Shipping fee:</td>
                                                    <td>₱</td>
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

                                    <a href="{{ route('cart.checkout') }}"
                                        class="btn btn-outline-primary-2 btn-order btn-block mb-2">PROCEED TO CHECKOUT</a>
                                    <a href="{{ route('shop.index') }}"
                                        class="btn btn-outline-dark-2 btn-block "><span>CONTINUE SHOPPING</span><i
                                            class="icon-refresh"></i></a>
                                </div>
                            </aside>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End .page-content -->

        </section>
    </main>

@endsection

@push('scripts')
    <script>
        $(function() {
            $(".qty-control__increase").on("click", function() {
                $(this).closest('form').submit();
            });

            $(".qty-control__reduce").on("click", function() {
                $(this).closest('form').submit();
            });

            $(".remove-cart").on("click", function() {
                $(this).closest('form').submit();
            });
        });
    </script>
@endpush
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const input = document.getElementById("checkout-discount-input");
        const label = document.querySelector(".discount-label");

        function toggleLabel() {
            if (input.value.trim() !== "") {
                label.classList.add("hidden");
            } else {
                label.classList.remove("hidden");
            }
        }

        input.addEventListener("focus", () => label.classList.add("hidden"));
        input.addEventListener("blur", toggleLabel);
        input.addEventListener("input", toggleLabel);

        // Run once on load
        toggleLabel();
    });
</script>
<style>
    /* Initial state */
    .discount-label {
        opacity: 1;
        transition: opacity 0.3s ease;
        pointer-events: auto;
    }

    /* Hidden state */
    .discount-label.hidden {
        opacity: 0;
        pointer-events: none;
        /* prevent clicks on hidden label */
    }

    .btn-remove-code {
        background-color: #e74c3c;
        /* red for remove */
        color: #fff;
        border: none;
        border-radius: 0 4px 4px 0;
        padding: 0.5rem 1rem;
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 0.875rem;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .btn-remove-code i {
        font-size: 0.9rem;
    }

    .btn-remove-code:hover {
        background-color: #c0392b;

    }

    .btn-remove-code:active {
        background-color: #a93226;
        transform: translateY(0);
    }
</style>
