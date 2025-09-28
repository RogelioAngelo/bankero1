@extends('layouts.app')
@section('content')
    <main>
        <section class="intro-slider-container">
            <div class="intro-slider owl-carousel owl-theme owl-nav-inside owl-light" data-toggle="owl"
                data-owl-options='{
            "dots": false,
            "nav": false,
            "loop": true,
            "autoplay": true,
            "autoplayTimeout": 5000,
            "responsive": {
                "992": {
                    "nav": true
                }
            }
        }'>
                @foreach ($slides as $slide)
                    <div class="intro-slide" style="background-image: url('{{ asset('uploads/slides/' . $slide->image) }}');">
                        <div class="container intro-content text-center">
                            <h3 class="intro-subtitle text-white">{{ $slide->tagline }}</h3>
                            <h1 class="intro-title text-white">{{ $slide->title }}</h1>
                            <h2 class="intro-title text-white fw-bold">{{ $slide->subtitle }}</h2>

                            <a href="{{ route('shop.index') }}" class="btn btn-outline-white-4">
                                <span>Shop Now</span>
                            </a>
                        </div><!-- End .intro-content -->
                    </div><!-- End .intro-slide -->
                @endforeach
            </div><!-- End .intro-slider owl-carousel owl-theme -->

            <!-- End .slider-loader -->
        </section>






        <div class="container mw-1620 bg-white border-radius-10">
            <div class="mb-3 mb-xl-5 pt-1 pb-4"></div>
            <div class="container categories pt-5 pt-lg-7">
                <h2 class="title text-center mb-4">Shop by Categories</h2>

                <div class="owl-carousel owl-simple" data-toggle="owl"
                    data-owl-options='{
            "nav": false,
            "dots": false,
            "margin": 30,
            "loop": false,
            "responsive": {
                "0": { "items":2 },
                "420": { "items":3 },
                "600": { "items":4 },
                "900": { "items":5 },
                "1024": { "items":6 }
            }
        }'>
                    @foreach ($categories as $category)
                        <a href="{{ route('shop.index', ['categories' => $category->id]) }}"
                            class="brand text-center d-flex flex-column align-items-center">
                            <img src="{{ asset('uploads/categories/' . $category->image) }}" alt="{{ $category->name }}"
                                class="mb-2" style="max-height:100px;">
                            <span class="fw-medium">{{ $category->name }}</span>
                        </a>
                    @endforeach
                </div><!-- End .owl-carousel -->
            </div>


            <section class="hot-deals container">
                <h2 class="section-title text-center mb-3 pb-xl-3 mb-xl-4">Hot Deals</h2>
                <div class="row">
                    <div
                        class="col-md-6 col-lg-4 col-xl-20per d-flex align-items-center flex-column justify-content-center py-4 align-items-md-start">
                        <h2>Summer Sale</h2>
                        <h2 class="fw-bold">Up to 60% Off</h2>

                        <div class="position-relative d-flex align-items-center text-center pt-xxl-4 js-countdown mb-3"
                            data-date="18-3-2024" data-time="06:50">
                            <div class="day countdown-unit">
                                <span class="countdown-num d-block"></span>
                                <span class="countdown-word text-uppercase text-secondary">Days</span>
                            </div>

                            <div class="hour countdown-unit">
                                <span class="countdown-num d-block"></span>
                                <span class="countdown-word text-uppercase text-secondary">Hours</span>
                            </div>

                            <div class="min countdown-unit">
                                <span class="countdown-num d-block"></span>
                                <span class="countdown-word text-uppercase text-secondary">Mins</span>
                            </div>

                            <div class="sec countdown-unit">
                                <span class="countdown-num d-block"></span>
                                <span class="countdown-word text-uppercase text-secondary">Sec</span>
                            </div>
                        </div>

                        <a href="{{ route('shop.index') }}"
                            class="btn-link default-underline text-uppercase fw-medium mt-3">View All</a>
                    </div>
                    <div class="col-md-6 col-lg-8 col-xl-80per">
                        <div class="position-relative">
                            <div class="swiper-container js-swiper-slider"
                                data-settings='{
                  "autoplay": {
                    "delay": 5000
                  },
                  "slidesPerView": 4,
                  "slidesPerGroup": 4,
                  "effect": "none",
                  "loop": false,
                  "breakpoints": {
                    "320": {
                      "slidesPerView": 2,
                      "slidesPerGroup": 2,
                      "spaceBetween": 14
                    },
                    "768": {
                      "slidesPerView": 2,
                      "slidesPerGroup": 3,
                      "spaceBetween": 24
                    },
                    "992": {
                      "slidesPerView": 3,
                      "slidesPerGroup": 1,
                      "spaceBetween": 30,
                      "pagination": false
                    },
                    "1200": {
                      "slidesPerView": 4,
                      "slidesPerGroup": 1,
                      "spaceBetween": 30,
                      "pagination": false
                    }
                  }
                }'>
                                <div class="swiper-wrapper">
                                    @foreach ($sproducts as $sproduct)
                                        <div class="swiper-slide product-card product-card_style3">
                                            <div class="pc__img-wrapper">
                                                <a
                                                    href="{{ route('shop.product.details', ['product_slug' => $sproduct->slug]) }}">
                                                    <img loading="lazy"
                                                        src="{{ asset('uploads/products') }}/{{ $sproduct->image }}"
                                                        width="258" height="313" alt="{{ $sproduct->image }}"
                                                        class="pc__img">

                                                </a>
                                            </div>

                                            <div class="pc__info position-relative">
                                                <h6 class="pc__title"><a
                                                        href="{{ route('shop.product.details', ['product_slug' => $sproduct->slug]) }}">{{ $sproduct->name }}</a>
                                                </h6>
                                                <div class="product-card__price d-flex">
                                                    <span class="money price text-secondary">
                                                        @if ($sproduct->sale_price)
                                                            <s>${{ $sproduct->regular_price }} </s>
                                                            ${{ $sproduct->sale_price }}
                                                        @else
                                                            ${{ $sproduct->regular_price }}
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div><!-- /.swiper-wrapper -->
                            </div><!-- /.swiper-container js-swiper-slider -->
                        </div><!-- /.position-relative -->
                    </div>
                </div>
            </section>







            <div class="bg-light-2 pt-6 pb-6 featured">
                <div class="container-fluid">
                    <div class="heading heading-center mb-3">
                        <h2 class="title">FEATURED PRODUCTS</h2><!-- End .title -->

                        {{-- <ul class="nav nav-pills justify-content-center" role="tablist">
                            @foreach ($categories as $key => $category)
                                <li class="nav-item">
                                    <a class="nav-link {{ $loop->first ? 'active' : '' }}"
                                        id="featured-{{ strtolower($category->id) }}-link" data-toggle="tab"
                                        href="#featured-{{ strtolower($category->name) }}-tab" role="tab"
                                        aria-controls="featured-{{ strtolower($category->name) }}-tab"
                                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                        {{ $category->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul> --}}

                    </div><!-- End .heading -->

                    <div class="tab-content tab-content-carousel">
                        <div class="tab-pane p-0 fade show active" id="featured-women-tab" role="tabpanel"
                            aria-labelledby="featured-women-link">
                            <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow"
                                data-toggle="owl"
                                data-owl-options='{
                                    "nav": false,
                                    "dots": true,
                                    "margin": 20,
                                    "loop": false,
                                    "responsive": {
                                        "0": {
                                            "items":2
                                        },
                                        "480": {
                                            "items":2
                                        },
                                        "768": {
                                            "items":3
                                        },
                                        "992": {
                                            "items":4
                                        },
                                        "1200": {
                                            "items":5,
                                            "nav": true
                                        }
                                    }
                                }'>
                                @foreach ($fproducts as $fproduct)
                                    <div class="product product-7">
                                        <figure class="product-media">
                                            @if ($fproduct->created_at->gt(now()->subDays(7)))
                                                <span class="product-label label-new">New</span>
                                            @endif
                                            <a
                                                href="{{ route('shop.product.details', ['product_slug' => $fproduct->slug]) }}">
                                                <div class="product-images">
                                                    <!-- Main product image -->
                                                    <div class="product-image-main">
                                                        <img src="{{ asset('uploads/products') }}/{{ $fproduct->image }}"
                                                            alt="{{ $fproduct->name }}" class="product-image">
                                                    </div>

                                                    <!-- Gallery images -->
                                                    @if ($fproduct->images)
                                                        @foreach (explode(',', $fproduct->images) as $gimg)
                                                            <div class="product-image-gallery">
                                                                <img src="{{ asset('uploads/products') }}/{{ $gimg }}"
                                                                    alt="{{ $fproduct->name }}"
                                                                    class="product-image-hover">
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>

                                            </a>

                                            <div class="product-action-vertical">
                                                @if (Cart::instance('wishlist')->content()->where('id', $fproduct->id)->count() > 0)
                                                    <!-- Already in wishlist → remove -->
                                                    <form method="POST"
                                                        action="{{ route('wishlist.item.remove', ['rowId' => Cart::instance('wishlist')->content()->where('id', $fproduct->id)->first()->rowId]) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn-product-icon btn-wishlist btn-expandable">
                                                            <span>Remove from wishlist</span>
                                                        </button>
                                                    </form>
                                                    <style>
                                                        .btn-wishlist {
                                                            border: none !important;

                                                            box-shadow: none !important;
                                                        }
                                                    </style>
                                                @else
                                                    <!-- Not in wishlist → add -->
                                                    <form method="POST" action="{{ route('wishlist.add') }}">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $fproduct->id }}">
                                                        <input type="hidden" name="name" value="{{ $fproduct->name }}">
                                                        <input type="hidden" name="price"
                                                            value="{{ $fproduct->sale_price && $fproduct->sale_price > 0 ? $fproduct->sale_price : $fproduct->regular_price }}">
                                                        <input type="hidden" name="quantity" value="1">
                                                        <button type="submit"
                                                            class="btn-product-icon btn-wishlist btn-expandable">
                                                            <span>Add to wishlist</span>
                                                        </button>
                                                    </form>
                                                    <style>
                                                        .btn-wishlist {
                                                            border: none !important;

                                                            box-shadow: none !important;
                                                        }
                                                    </style>
                                                @endif
                                                <a href="popup/quickView.html" class="btn-product-icon btn-quickview mt-1"
                                                    title="Quick view"><span>Quick view</span></a>
                                            </div><!-- End .product-action-vertical -->

                                            <div class="product-action">
                                                <div class="product-action">
                                                    @auth
                                                        @if (Cart::instance('cart')->content()->where('id', $fproduct->id)->count() > 0)
                                                            <!-- Already in cart → Go to Cart -->
                                                            <a href="{{ route('cart.index') }}" class="btn-product btn-cart">
                                                                <span>Go to Cart</span>
                                                            </a>
                                                        @else
                                                            <!-- Not in cart → Add to Cart -->
                                                            <form name="addtocart-form" method="POST"
                                                                action="{{ route('cart.add') }}" class="w-100 m-0 p-0">
                                                                @csrf
                                                                <input type="hidden" name="id"
                                                                    value="{{ $fproduct->id }}">
                                                                <input type="hidden" name="quantity" value="1">
                                                                <input type="hidden" name="name"
                                                                    value="{{ $fproduct->name }}">
                                                                <input type="hidden" name="price"
                                                                    value="{{ $fproduct->sale_price && $fproduct->sale_price > 0 ? $fproduct->sale_price : $fproduct->regular_price }}">
                                                                <button type="submit"
                                                                    class="btn-product btn-cart w-100 border-0">
                                                                    <span>Add to cart</span>
                                                                </button>
                                                            </form>
                                                            <style>
                                                                .product-action form {
                                                                    display: flex;
                                                                }

                                                                .product-action button {
                                                                    flex: 1;
                                                                    /* button stretches */
                                                                }
                                                            </style>
                                                        @endif
                                                    @else
                                                        <!-- Guest → must login -->

                                                        <a href="#signin-modal" data-toggle="modal" class="btn-product btn-cart"><span>add to cart
                                                            </span></a>

                                                    @endauth
                                                </div>

                                            </div><!-- End .product-action -->
                                        </figure><!-- End .product-media -->

                                        <div class="product-body">
                                            <h3 class="product-title"><a href="product.html">{{ $fproduct->name }}</a>
                                            </h3>
                                            <!-- End .product-title -->
                                            <div class="product-price">
                                                @if ($fproduct->sale_price)
                                                    <s>₱{{ $fproduct->regular_price }} </s> ₱{{ $fproduct->sale_price }}
                                                @else
                                                    ₱{{ $fproduct->regular_price }}
                                                @endif
                                            </div><!-- End .product-price -->
                                            @php
                                                $reviewCount = $fproduct->reviews->count();
                                                $avg = $reviewCount ? $fproduct->reviews->avg('rating') : 0;
                                                $percent = $reviewCount ? ($avg / 5) * 100 : 0;
                                            @endphp

                                            <div class="ratings-container">
                                                @if ($reviewCount > 0)
                                                    <div class="ratings">
                                                        <div class="ratings-val" style="width: {{ $percent }}%;">
                                                        </div>
                                                    </div><!-- End .ratings -->
                                                    <span class="ratings-text">
                                                        ({{ $reviewCount }} {{ Str::plural('Review', $reviewCount) }})
                                                    </span>
                                                @else
                                                    <span class="ratings-text">No reviews yet</span>
                                                @endif
                                            </div>
                                            <!-- End .rating-container -->
                                        </div><!-- End .product-body -->
                                    </div><!-- End .product -->
                                @endforeach

                            </div><!-- End .owl-carousel -->
                        </div><!-- .End .tab-pane -->
                    </div><!-- End .tab-content -->
                </div><!-- End .container-fluid -->
            </div>
        </div>
    </main>
@endsection
