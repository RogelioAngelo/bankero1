@extends('layouts.app')
@section('content')
    <style>
        .table> :not(caption)>tr>th {
            padding: 0.625rem 1.5rem .625rem !important;
            background-color: #6a6e51 !important;
        }

        .table>tr>td {
            padding: 0.625rem 1.5rem .625rem !important;
        }

        .table-bordered> :not(caption)>tr>th,
        .table-bordered> :not(caption)>tr>td {
            border-width: 1px 1px;
            border-color: #6a6e51;
        }

        .table> :not(caption)>tr>td {
            padding: .8rem 1rem !important;
        }

        .bg-success {
            background-color: #40c710 !important;
        }

        .bg-danger {
            background-color: #f44032 !important;
        }

        .bg-warning {
            background-color: #f5d700 !important;
            color: #000;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Use event delegation to handle dropdowns reliably
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.more-button');

                if (btn) {
                    e.stopPropagation();
                    const dropdown = btn.nextElementSibling;

                    // Close other dropdowns
                    document.querySelectorAll('.dropdown-content').forEach(menu => {
                        if (menu !== dropdown) menu.classList.add('hidden');
                    });

                    const isHidden = dropdown.classList.contains('hidden');
                    dropdown.classList.toggle('hidden');
                    btn.setAttribute('aria-expanded', String(!isHidden));
                    return;
                }

                // Click outside any button: close all
                document.querySelectorAll('.dropdown-content').forEach(menu => menu.classList.add(
                    'hidden'));
                document.querySelectorAll('.more-button').forEach(b => b.setAttribute('aria-expanded',
                    'false'));
            });

            // Close on Escape key
            window.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' || e.key === 'Esc') {
                    document.querySelectorAll('.dropdown-content').forEach(menu => menu.classList.add(
                        'hidden'));
                    document.querySelectorAll('.more-button').forEach(b => b.setAttribute('aria-expanded',
                        'false'));
                }
            });
        });
    </script>
    <main class="pt-90" style="padding-top: 0px">
        <section class="my-account container ">
            <nav aria-label="breadcrumb" class="breadcrumb-nav mb-1">
                <div class="container">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                        <li class="breadcrumb-item"><a href="#">Shop</a></li>
                        <li class="breadcrumb-item active" aria-current="page">My Account</li>
                    </ol>
                </div><!-- End .container -->
            </nav>

            <div class="row">
                <div class="col-lg-2 mt-2">
                    <div class="sticky-sidebar">
                        <h2 class="page-title mt-1">Orders</h2>
                        @include('user.account-nav')
                    </div>
                </div>

                <div class="col-lg-10 mt-5">
                    @php
                        $currentStatus = $status ?? 'ordered';
                    @endphp
                    <ul class="nav nav-tabs order-nav" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $currentStatus === 'ordered' ? 'active' : '' }}"
                                href="{{ route('user.orders', ['status' => 'ordered']) }}">Ordered</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $currentStatus === 'delivered' ? 'active' : '' }}"
                                href="{{ route('user.orders', ['status' => 'delivered']) }}">Delivered</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $currentStatus === 'canceled' ? 'active' : '' }}"
                                href="{{ route('user.orders', ['status' => 'canceled']) }}">Canceled</a>
                        </li>
                    </ul>


                    <div id="order-container" class="order-container">

                        @forelse ($orders as $order)
                            <div class="order-card"> <!-- Added wrapper + spacing -->
                                <!-- Status Header -->
                                <div class="flex-center-y flex-space-x-2 status-text mb-3">
                                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>

                                    @if ($order->status === 'delivered')
                                        <span class="delivered">Parcel has been delivered</span>
                                        <span class="separator">|</span>
                                        <span class="completed">COMPLETED</span>
                                    @elseif($order->status === 'canceled')
                                        <span class="canceled">Order Canceled</span>
                                    @else
                                        <span class="ordered">Order Placed</span>
                                    @endif
                                </div>

                                <!-- Loop Products -->
                                @foreach ($order->orderItems as $item)
                                    <div class="product-item border-line mb-4">
                                        <div class="flex-start-y">
                                            <!-- Product Image -->
                                            <div class="img-wrapper">
                                                <a
                                                    href="{{ route('shop.product.details', ['product_slug' => $item->product->slug]) }}">
                                                    <img src="{{ asset('uploads/products/thumbnails/' . ($item->product->image ?? 'placeholder.png')) }}"
                                                        onerror="this.onerror=null;this.src='{{ asset('uploads/products/thumbnails/placeholder.png') }}';"
                                                        alt="{{ $item->product->name ?? 'Product' }}">
                                                </a>
                                            </div>

                                            <!-- Product Info -->
                                            <div class="info flex flex-row items-center gap-4 mt-1">
                                                <a href="{{ route('shop.product.details', ['product_slug' => $item->product->slug]) }}"
                                                    class="title hover:underline">
                                                    {{ $item->product->name ?? 'Product' }}
                                                </a>
                                                <p class="variation"> Variation: {{ $item->variation ?? '-' }}</p>
                                                <p class="quantity">x{{ $item->quantity }}</p>
                                            </div>

                                        </div>

                                        <!-- Product Price -->
                                        <div class="price">₱{{ number_format($item->price, 2) }}</div>
                                    </div>
                                @endforeach

                                <!-- Footer -->
                                <div class="order-footer mt-4">
                                    <!-- Order Total -->
                                    <div class="order-total-row mb-2">
                                        <span class="label">Order Total:</span>
                                        <span class="amount">₱{{ number_format($order->total, 2) }}</span>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="action-container">
                                        <!-- Rating Info -->
                                        <div class="rating-info">
                                            <p class="date">
                                                Order date <span class="font-semibold text-gray-700">
                                                    {{ $order->created_at }}
                                                </span>
                                            </p>
                                            @if ($order->status === 'delivered' && !empty($order->delivered_date))
                                                <p class="date">Delivered on <span>{{ $order->delivered_date }}</span>
                                                </p>
                                            @endif
                                        </div>

                                        <!-- Buttons -->
                                        <div class="button-group">
                                            @if ($order->status === 'delivered')
                                                @if (\Illuminate\Support\Facades\Route::has('user.order.rate'))
                                                    <a href="{{ route('user.order.rate', ['order_id' => $order->id]) }}"
                                                        class="action-button rate-button">Rate</a>
                                                @else
                                                    {{-- Fallback: route missing, send user to order details (reviews tab) --}}
                                                    <a href="{{ route('user.order.details', ['order_id' => $order->id]) }}#product-review-tab"
                                                        class="action-button rate-button">Rate</a>
                                                @endif
                                            @endif
                                            <a href="{{ route('user.order.details', ['order_id' => $order->id]) }}"
                                                class="action-button view-button">View Details</a>
                                            {{-- <button class="action-button refund-button">Request Return/Refund</button> --}}
                                            <div class="relative">
                                                <button type="button" class="more-button">
                                                    More
                                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </button>
                                                <div class="dropdown-content hidden">
                                                    <a href="#">Contact Seller</a>
                                                    <a
                                                        href="{{ route('shop.product.details', ['product_slug' => $item->product->slug]) }}">
                                                        Buy Again
                                                    </a>
                                                </div>
                                            </div>


                                            {{-- dropdown script removed from inside loop; consolidated script is appended once at the end of the page for better reliability --}}



                                        </div>
                                    </div>
                                </div>
                            </div> <!-- END order-card -->

                        @empty
                            <p class="text-center">No orders found.</p>
                        @endforelse

                        <div class="divider"></div>
                        <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                            {{ $orders->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <style>
        .dropdown-content {
            position: absolute;
            right: 0;
            top: 100%;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 0.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            min-width: 150px;
            z-index: 100;
        }

        .dropdown-content a {
            display: block;
            padding: 10px 14px;
            color: #333;
            text-decoration: none;
        }

        .dropdown-content a:hover {
            background: #f5f5f5;
        }

        .order-card {
            background: #fff;
            border-radius: 0.75rem;
            padding: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 24px;
            /* <-- adds space below each card */
        }

        /* Sticky left sidebar for account navigation */
        .sticky-sidebar {
            position: sticky;
            top: 80px;
            /* adjust this if your header height is different */
        }

        @media (max-width: 767px) {
            .sticky-sidebar {
                position: static;
            }
        }

        /* Navbar (not sticky) */
        .order-nav {
            position: static;
            background: #fff;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e5e7eb;
        }

        /* Active tab style */
        .order-nav .nav-link.active {
            background-color: #ef4444;
            /* red highlight */
            color: #fff !important;
            border-radius: 0.5rem;
            font-weight: 600;
        }

        /* Hover effect */
        .order-nav .nav-link:hover {
            background-color: #f3f4f6;
            border-radius: 0.5rem;
        }

        @media (min-width: 640px) {

            /* sm breakpoint */
            .order-container {
                padding: 24px;
                /* sm:p-6 */
            }
        }

        /* Status Badge */
        .status-badge {
            padding: 2px 8px;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 4px;
            display: inline-block;
            background-color: #fee2e2;
            /* bg-red-100 */
            color: #b91c1c;
            /* text-red-700 */
        }

        /* Store Header (Equivalent of flex flex-col sm:flex-row sm:items-center justify-between border-b border-gray-200 pb-3 mb-4 space-y-3 sm:space-y-0) */
        .store-header {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: space-between;
            border-bottom: 1px solid #e5e7eb;
            /* border-b border-gray-200 */
            padding-bottom: 12px;
            /* pb-3 */
            margin-bottom: 16px;
            /* mb-4 */
            gap: 12px;
            /* space-y-3 */
        }

        @media (min-width: 640px) {
            .store-header {
                flex-direction: row;
                align-items: center;
                gap: 0;
            }
        }

        /* Utility for Flex Alignment */
        .flex-center-y {
            display: flex;
            align-items: center;
        }

        .flex-start-y {
            display: flex;
            align-items: flex-start;
        }

        .flex-space-x-3>*+* {
            margin-left: 0.75rem;
        }

        .flex-space-x-2>*+* {
            margin-left: 0.5rem;
        }

        /* Store Action Links */
        .store-action {
            font-size: 0.875rem;
            /* text-sm */
            font-weight: 600;
            text-decoration: none;
            transition: color 150ms ease-in-out;
        }

        .store-action-chat {
            color: #dc2626;
            /* text-red-600 */
            border: 1px solid #dc2626;
            padding: 4px 8px;
            border-radius: 9999px;
            /* rounded-full */
            transition: background-color 150ms ease-in-out;
        }

        .store-action-chat:hover {
            background-color: #fef2f2;
            /* hover:bg-red-50 */
        }

        .store-action-shop {
            color: #6b7280;
            /* text-gray-500 */
        }

        .store-action-shop:hover {
            color: #1f2937;
            /* hover:text-gray-800 */
        }

        /* Delivery Status */
        .status-text {
            font-size: 0.875rem;
            font-weight: 600;
        }

        .status-text .icon {
            width: 20px;
            height: 20px;
            color: #16a34a;
            /* text-green-600 */
        }

        .status-text .delivered {
            color: #16a34a;
            /* text-green-600 */
        }

        .status-text .separator {
            color: #9ca3af;
            /* text-gray-400 */
        }

        .status-text .completed {
            color: #dc2626;
            /* text-red-600 */
        }

        /* Product Item Layout */
        .product-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-top: 12px;
            padding-bottom: 12px;
        }

        .product-item.border-line {
            border-bottom: 1px solid #f3f4f6;
            /* border-gray-100 */
        }

        .product-item .img-wrapper {
            width: 80px;
            height: 80px;
            flex-shrink: 0;
        }

        .product-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 0.5rem;
            /* rounded-lg */
            border: 1px solid #f3f4f6;
        }

        .product-item .info {
            display: flex;
            flex-direction: column;
            margin-left: 1rem;
            /* space-x-4 equivalent */
            gap: 0.25rem;
            /* allow the info column to take available space and shrink properly */
            flex: 1 1 auto;
            min-width: 0;
        }

        /* On medium+ screens show info items in a row and center them vertically */
        @media (min-width: 640px) {
            .product-item .info {
                flex-direction: row;
                align-items: center;
                gap: 1rem;
            }

            .product-item .variation,
            .product-item .quantity {
                margin-top: 0;
            }
        }

        .product-item .title {
            color: #1f2937;
            /* text-gray-800 */
            font-weight: 600;
            font-size: 1.125rem;
            /* slightly larger */
            line-height: 1.4;
            /* Truncate long product titles so they don't push the price out */
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 40ch;
        }

        .product-item .variation {
            color: #6b7280;
            /* text-gray-500 */
            font-size: 0.95rem;
            /* slightly bigger than before */
            margin-top: 0;
        }

        .product-item .quantity {
            color: #4b5563;
            /* text-gray-600 */
            font-size: 0.95rem;
            margin-top: 0;
            font-weight: 600;
        }

        /* Bump sizes a bit more on wider screens */
        @media (min-width: 1024px) {
            .product-item .title {
                font-size: 1.25rem;
            }

            .product-item .variation,
            .product-item .quantity {
                font-size: 1rem;
            }
        }

        .product-item .price {
            color: #1f2937;
            font-weight: 600;
            font-size: 1.125rem;
            /* text-lg */
            /* keep price on a single line and prevent it from shrinking */
            flex: 0 0 auto;
            white-space: nowrap;
            padding-left: 1rem;
            /* pl-4 */
        }

        /* Footer (Order Total and Actions) */
        .order-footer {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
        }

        .order-total-row {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 16px;
            padding-right: 0;
        }

        @media (min-width: 640px) {
            .order-total-row {
                padding-right: 8px;
            }
        }

        .order-total-row .label {
            font-size: 1.125rem;
            color: #4b5563;
            font-weight: 500;
            margin-right: 1rem;
        }

        .order-total-row .amount {
            font-size: 1.5rem;
            /* text-2xl */
            color: #dc2626;
            font-weight: 700;
        }

        /* Action Buttons Container */
        .action-container {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            justify-content: space-between;
            gap: 12px;
            /* space-y-3 */
        }

        @media (min-width: 640px) {
            .action-container {
                flex-direction: row;
                align-items: center;
                gap: 0;
            }
        }

        /* Rating Info */
        .rating-info {
            font-size: 0.875rem;
            /* allow rating/date area to take remaining space so buttons stay right aligned */
            flex: 1 1 auto;
            min-width: 0;
        }

        .rating-info .date {
            color: #6b7280;
        }

        .rating-info .date span {
            font-weight: 600;
            color: #4b5563;
        }

        .rating-info .coins {

            font-weight: 500;
            margin-top: 2px;
        }

        .rating-info .coins span {
            color: #f97316;
            /* orange-500 */
            font-weight: 700;
        }

        /* Buttons */
        .button-group {
            display: flex;
            gap: 0.5rem;
            width: 100%;
            justify-content: flex-end;
        }

        @media (min-width: 640px) {
            .button-group {
                width: auto;
            }
        }

        .action-button {
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 0.5rem;
            transition: all 150ms ease-in-out;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.06);
            cursor: pointer;
            flex-grow: 1;
            /* flex-1 */
        }

        @media (min-width: 640px) {
            .action-button {
                flex-grow: 0;
                /* sm:flex-none */
            }
        }

        .rate-button {
            background-color: #dc2626;
            color: #ffffff;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        .rate-button:hover {
            background-color: #b91c1c;
            /* hover:bg-red-700 */
        }

        .refund-button {
            background-color: #ffffff;
            border: 1px solid #d1d5db;
            /* border-gray-300 */
            color: #4b5563;
            /* text-gray-700 */
            padding-left: 1rem;
            padding-right: 1rem;
            box-shadow: none;
            /* remove shadow for cleaner look */
        }

        .refund-button:hover {
            background-color: #f9fafb;
            /* hover:bg-gray-50 */
        }

        .view-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            background-color: #222;
            color: #fff;
            padding: 8px 14px;
            border-radius: 0.5rem;
            font-weight: 600;
        }

        .view-button:hover {
            background-color: #fff;
            color: #222;
            border: #222 solid 1px;
        }

        /* More Button Dropdown */
        .relative {
            position: relative;
        }

        .more-button {
            background-color: #ffffff;
            border: 1px solid #d1d5db;
            color: #4b5563;
            border: #222 solid 1px;
            padding: 8px 12px;
            border-radius: 0.5rem;
            transition: background-color 150ms ease-in-out;
            display: flex;
            align-items: center;
            cursor: pointer;
            box-shadow: none;
        }

        .more-button:hover {
            background-color: #222;
            color: #fff;

        }

        .dropdown-content {
            position: absolute;
            right: 0;
            margin-top: 8px;
            width: 12rem;
            /* w-48 */
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            z-index: 10;
        }

        .dropdown-content a {
            display: block;
            padding: 8px 16px;
            font-size: 0.875rem;
            color: #4b5563;
            text-decoration: none;
        }

        .dropdown-content a:hover {
            background-color: #f3f4f6;
            /* hover:bg-gray-100 */
        }

        /* 'hidden' class for JS */
        .hidden {
            display: none !important;
        }
    </style>
@endsection
