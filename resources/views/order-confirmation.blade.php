@extends('layouts.app')
@section('content')
    <main class="pt-90">
        <div class="mb-4 pb-4"></div>
        <section class="shop-checkout container">
            <h2 class="page-title">Order Confirmation</h2>
            {{-- <div class="checkout-steps">
        <a href="javascript:void(0)" class="checkout-steps__item active">
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
        <a href="javascript:void(0)" class="checkout-steps__item active">
          <span class="checkout-steps__item-number">03</span>
          <span class="checkout-steps__item-title">
            <span>Confirmation</span>
            <em>Review And Submit Your Order</em>
          </span>
        </a>
      </div> --}}
            <div class="order-complete">
                <div class="order-complete__message">
                    <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="40" cy="40" r="40" fill="#B9A16B" />
                        <path
                            d="M52.9743 35.7612C52.9743 35.3426 52.8069 34.9241 52.5056 34.6228L50.2288 32.346C49.9275 32.0446 49.5089 31.8772 49.0904 31.8772C48.6719 31.8772 48.2533 32.0446 47.952 32.346L36.9699 43.3449L32.048 38.4062C31.7467 38.1049 31.3281 37.9375 30.9096 37.9375C30.4911 37.9375 30.0725 38.1049 29.7712 38.4062L27.4944 40.683C27.1931 40.9844 27.0257 41.4029 27.0257 41.8214C27.0257 42.24 27.1931 42.6585 27.4944 42.9598L33.5547 49.0201L35.8315 51.2969C36.1328 51.5982 36.5513 51.7656 36.9699 51.7656C37.3884 51.7656 37.8069 51.5982 38.1083 51.2969L40.385 49.0201L52.5056 36.8996C52.8069 36.5982 52.9743 36.1797 52.9743 35.7612Z"
                            fill="white" />
                    </svg>
                    <h3>Your order is completed!</h3>
                    <p>Thank you. Your order has been received.</p>
                </div>
                <div class="order-info">
                    <div class="order-info__item">
                        <label>Order Number</label>
                        <span>{{ $order->id }}</span>
                    </div>
                    <div class="order-info__item">
                        <label>Date</label>
                        <span>{{ $order->created_at }}</span>
                    </div>
                    <div class="order-info__item">
                        <label>Total</label>
                        <span>₱{{ $order->total }}</span>
                    </div>
                    <div class="order-info__item">
                        <label>Payment Method</label>
                        <span> {{ $order->transaction->mode }} </span>
                    </div>
                </div>
                <div class="checkout__totals-wrapper">
                    <div class="checkout__totals">
                        <h3>Order Details</h3>
                        <table class="checkout-cart-items">
                            <thead>
                                <tr>
                                    <th>PRODUCT</th>
                                    <th>SUBTOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->orderItems as $item)
                                    <tr>
                                        <td>
                                            {{ $item->product->name }} x {{ $item->quantity }}
                                        </td>
                                        <td class="text-right">
                                            ₱{{ $item->price }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <table class="checkout-totals">
                            <tbody>
                                <tr>
                                    <th>SUBTOTAL</th>
                                    <td class="text-right">₱{{ $order->subtotal }} </td>
                                </tr>
                                <tr>
                                    <th>DISCOUNT</th>
                                    <td class="text-right">₱{{ $order->discount }} </td>
                                </tr>
                                <tr>
                                    <th>SHIPPING</th>
                                    <td class="text-right">Free shipping</td>
                                </tr>
                                <tr>
                                    <th>VAT</th>
                                    <td class="text-right">₱{{ $order->tax }} </td>
                                </tr>
                                <tr>
                                    <th>TOTAL</th>
                                    <td class="text-right">₱{{ $order->total }} </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
<style>
    .checkout-form {
        display: flex;
        gap: 3.625rem;
    }

    @media (max-width: 1199.98px) {
        .checkout-form {
            flex-direction: column;
        }
    }

    .checkout-form .billing-info__wrapper {
        padding-top: 3.125rem;
        flex-grow: 1;
    }

    .checkout-form .billing-info__wrapper .form-floating>label,
    .checkout-form .billing-info__wrapper .form-label-fixed>.form-label {
        color: #767676;
    }

    .checkout-form .checkout__totals-wrapper .sticky-content {
        padding-top: 3.125rem;
    }

    .checkout-form .checkout__totals-wrapper .btn-checkout {
        width: 100%;
        height: 3.75rem;
        font-size: 0.875rem;
    }

    .checkout-form .checkout__payment-methods {
        border: 1px solid #e4e4e4;
        margin-bottom: 1.25rem;
        padding: 2.5rem 2.5rem 1.5rem;
        width: 26.25rem;
    }

    @media (max-width: 1199.98px) {
        .checkout-form .checkout__payment-methods {
            width: 100%;
        }
    }

    .checkout-form .checkout__payment-methods label {
        font-size: 1rem;
        line-height: 1.5rem;
    }

    .checkout-form .checkout__payment-methods label .option-detail {
        font-size: 0.875rem;
        margin: 0.625rem 0 0;
        display: none;
    }

    .checkout-form .checkout__payment-methods .form-check-input:checked~label .option-detail {
        display: block;
    }

    .checkout-form .checkout__payment-methods .policy-text {
        font-size: 0.75rem;
        line-height: 1.5rem;
    }

    .checkout-form .checkout__payment-methods .policy-text>a {
        color: #c32929;
    }

    .checkout__totals {
        border: 1px solid #222;
        margin-bottom: 1.25rem;
        padding: 2.5rem 2.5rem 0.5rem;
        width: 26.25rem;
    }

    @media (max-width: 1199.98px) {
        .checkout__totals {
            width: 100%;
        }
    }

    .checkout__totals>h3,
    .checkout__totals>.h3 {
        font-size: 1rem;
        text-transform: uppercase;
        margin-bottom: 1.25rem;
    }

    .checkout__totals table {
        width: 100%;
    }

    .checkout__totals .checkout-cart-items thead th {
        border-bottom: 1px solid #e4e4e4;
        padding: 0.875rem 0;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .checkout__totals .checkout-cart-items tbody td {
        padding: 0.40625rem 0;
        color: #767676;
    }

    .checkout__totals .checkout-cart-items tbody tr:first-child td {
        padding-top: 0.8125rem;
    }

    .checkout__totals .checkout-cart-items tbody tr:last-child td {
        padding-bottom: 0.8125rem;
        border-bottom: 1px solid #e4e4e4;
    }

    .checkout__totals .checkout-totals th,
    .checkout__totals .checkout-totals td {
        border-bottom: 1px solid #e4e4e4;
        padding: 0.875rem 0;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .checkout__totals .checkout-totals tr:last-child th,
    .checkout__totals .checkout-totals tr:last-child td {
        border-bottom: 0;
    }

    .order-complete {
        width: 56.25rem;
        max-width: 100%;
        margin: 3.125rem auto;
        display: flex;
        flex-direction: column;
        gap: 2.25rem;
    }

    .order-complete__message {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .order-complete__message svg {
        margin-bottom: 1.25rem;
    }

    .order-complete__message h3,
    .order-complete__message .h3 {
        font-size: 2.1875rem;
        text-align: center;
    }

    .order-complete__message p {
        color: #767676;
        margin-bottom: 0;
        text-align: center;
    }

    .order-complete {
    border: 2px solid #767676;
    padding: 2.5rem;
    display: flex;
    gap: 1rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    background-color: #fff; /* Ensures the background is white like a receipt */
    border-radius: 5px; /* Optional: rounded corners for a softer look */
}


    .order-complete .order-info {
        width: 100%;
        border: 2px dashed #767676;
        padding: 2.5rem;
        display: flex;
        gap: 1rem;
    }

    @media (max-width: 767.98px) {
        .order-complete .order-info {
            flex-direction: column;
        }
    }

    .order-complete .order-info__item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        flex-grow: 1;
    }

    .order-complete .order-info__item label {
        font-size: 0.875rem;
        font-weight: 400;
        color: #767676;
    }

    .order-complete .order-info__item span {
        font-size: 1rem;
        font-weight: 500;
    }

    .order-complete .checkout__totals {
        width: 100%;
    }

    .order-complete .checkout__totals .checkout-cart-items thead th:last-child {
        text-align: right;
    }

    .order-tracking {
        width: 31.25rem;
        max-width: 100%;
        margin: 0 auto;
        text-align: center;
    }

    .order-tracking .btn-track {
        height: 3.75rem;
        font-size: 0.875rem;
    }
</style>
