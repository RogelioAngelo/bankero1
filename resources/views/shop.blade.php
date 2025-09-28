@extends('layouts.app')
@section('content')
    <style>
        .brand-list li,
        .category-list li {
            line-height: 40px;
        }

        .brand-list li .chk-brand,
        .category-list li .chk-category {
            width: 1rem;
            height: 1rem;
            color: #e4e4e4;
            border: 0.12rem solid currentColor;
            border-radius: 0;
            margin-right: 0.75rem;
        }

        .filled-heart {
            color: orange;
        }
    </style>
    <main class="pt-90">
        <nav aria-label="breadcrumb" class="breadcrumb-nav border-0 mb-0">
            <div class="container d-flex align-items-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Shop</a></li>
                </ol>
            </div><!-- End .container -->
        </nav>

        <div class="page-content">
            <div class="container">
                <div class="toolbox">
                    <div class="toolbox-left">
                        <a href="#" class="sidebar-toggler"><i class="icon-bars"></i>Filters</a>
                    </div>

                    <div class="toolbox-center">
                        <div class="toolbox-info">
                            Showing <span>{{ $products->count() }} of {{ $products->total() }}</span> Products
                        </div>
                    </div>

                    <div class="toolbox-right">
                        <div class="toolbox-sort">
                            <label for="sortby">Show:</label>
                            <div class="select-custom">
                                <select class="shop-acs__select form-select w-auto border-0 py-0 order-1 order-md-0"
                                    aria-label="Page Size" id="pagesize" name="pagesize" style="margin-right:20px;">
                                    <option value="8" {{ $size == 8 ? 'selected' : '' }}>Show</option>
                                    <option value="12" {{ $size == 12 ? 'selected' : '' }}>12</option>
                                    <option value="16" {{ $size == 16 ? 'selected' : '' }}>16</option>
                                    <option value="20" {{ $size == 20 ? 'selected' : '' }}>20</option>

                                </select>
                            </div>
                        </div>
                        <div class="toolbox-sort">
                            <label for="sortby">Sort by:</label>
                            <div class="select-custom">
                                <select class="form-control" aria-label="Sort Items" name="orderby" id="orderby">
                                    <option value="-1" {{ $order == -1 ? 'selected' : '' }}>Default</option>
                                    <option value="1" {{ $order == 1 ? 'selected' : '' }}>Date, New to Old</option>
                                    <option value="2" {{ $order == 2 ? 'selected' : '' }}>Date, Old to New</option>
                                    <option value="3" {{ $order == 3 ? 'selected' : '' }}>Price, Low to High</option>
                                    <option value="4" {{ $order == 4 ? 'selected' : '' }}>Price, High to Low</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>


        <section class="shop-main container d-flex pt-4 pt-xl-5">
            <div class="products">
                <div class="row">
                    @foreach ($products as $product)
                        <div class="col-6 col-md-4 col-lg-4 col-xl-3 col-xxl-2">
                            <div class="product product-7">
                                <figure class="product-media">
                                    @php
                                        $discount = null;
                                        if ($product->sale_price && $product->sale_price < $product->regular_price) {
                                            $discount = round(
                                                (($product->regular_price - $product->sale_price) /
                                                    $product->regular_price) *
                                                    100,
                                            );
                                        }
                                    @endphp

                                    @if ($discount)
                                        <span class="product-label label-sale">-{{ $discount }}%</span>
                                    @elseif ($product->created_at->gt(now()->subDays(7)))
                                        <span class="product-label label-new">New</span>
                                    @endif

                                    {{-- <a href="{{ route('shop.product.details', ['product_slug' => $product->slug]) }}">
                                        <img src="{{ asset('uploads/products') }}/{{ $product->image }}"
                                            alt="{{ $product->name }}" class="product-image">
                                    </a> --}}

                                    <div class="product-images">
                                        <!-- Main product image -->
                                        <div class="product-image-main">
                                            <img src="{{ asset('uploads/products') }}/{{ $product->image }}"
                                                alt="{{ $product->name }}" class="product-image">
                                        </div>

                                        <!-- Gallery images -->
                                        @if ($product->images)
                                            @foreach (explode(',', $product->images) as $gimg)
                                                <div class="product-image-gallery">
                                                    <a
                                                        href="{{ route('shop.product.details', ['product_slug' => $product->slug]) }}">
                                                        <img src="{{ asset('uploads/products') }}/{{ $gimg }}"
                                                            alt="{{ $product->name }}" class="product-image-hover">
                                                    </a>
                                                </div>
                                            @endforeach
                                        @endif

                                    </div>


                                    <div class="product-action-vertical">
                                        {{-- Wishlist --}}
                                        @if (Cart::instance('wishlist')->content()->where('id', $product->id)->count() > 0)
                                            <form method="POST"
                                                action="{{ route('wishlist.item.remove', ['rowId' => Cart::instance('wishlist')->content()->where('id', $product->id)->first()->rowId]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn-product-icon btn-wishlist btn-expandable active">
                                                    <span>remove from wishlist</span>
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('wishlist.add') }}">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $product->id }}">
                                                <input type="hidden" name="name" value="{{ $product->name }}">
                                                <input type="hidden" name="price"
                                                    value="{{ $product->sale_price ?: $product->regular_price }}">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit"
                                                    class="btn-product-icon btn-wishlist btn-expandable border-0 ">
                                                    <span>add to wishlist</span>
                                                </button>

                                            </form>
                                            <style>
                                                .btn-wishlist {
                                                    border: none !important;

                                                    box-shadow: none !important;
                                                }
                                            </style>
                                        @endif
                                    </div>

                                    <div class="product-action action-icon-top">
                                        @auth
                                            @if (Cart::instance('cart')->content()->where('id', $product->id)->count() > 0)
                                                <a href="{{ route('cart.index') }}" class="btn-product btn-cart">
                                                    <span>Go to cart</span>
                                                </a>
                                            @else
                                                <form name="addtocart-form" method="POST" action="{{ route('cart.add') }}"
                                                    class="w-100 m-0 p-0">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $product->id }}">
                                                    <input type="hidden" name="quantity" value="1">
                                                    <input type="hidden" name="name" value="{{ $product->name }}">
                                                    <input type="hidden" name="price"
                                                        value="{{ $product->sale_price && $product->sale_price > 0 ? $product->sale_price : $product->regular_price }}">
                                                    <button type="submit" class="btn-product btn-cart w-100 border-0">
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
                                            <a href="#signin-modal" data-toggle="modal"
                                                class="btn-product btn-cart"><span>add to cart
                                                </span></a>
                                        @endauth
                                    </div>
                                </figure>

                                <div class="product-body">
                                    <div class="product-cat">
                                        <a href="#">{{ $product->category->name }}</a>
                                    </div>
                                    <h3 class="product-title">
                                        <a href="{{ route('shop.product.details', ['product_slug' => $product->slug]) }}">
                                            {{ $product->name }}
                                        </a>
                                    </h3>
                                    <div class="product-price">
                                        @if ($product->sale_price)
                                            <s>₱{{ $product->regular_price }}</s> ₱{{ $product->sale_price }}
                                        @else
                                            ₱{{ $product->regular_price }}
                                        @endif
                                    </div>
                                    <div class="ratings-container">
                                        @php
                                            $reviewCount = $product->reviews->count();
                                        @endphp

                                        @if ($reviewCount > 0)
                                            <span class="ratings-text">({{ $reviewCount }}
                                                {{ Str::plural('Review', $reviewCount) }})</span>
                                        @else
                                            <span class="ratings-text">No reviews yet</span>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
                @if ($products->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $products->appends(request()->query())->links('pagination.pagination') }}
                    </div>
                @endif

            </div>


            <div class="sidebar-filter-overlay" id="sidebarFilterOverlay">
                <aside class="sidebar-shop sidebar-filter" id="sidebarShop">
                    <div class="sidebar-filter-wrapper">

                        <div class="widget widget-clean">
                            <label id="filterCloseBtn"><i class="icon-close"></i>Filters</label>
                            <a href="#" class="sidebar-filter-clear" id="filterClearBtn">Clean All</a>
                        </div>

                        <form id="filterForm" method="GET" action="{{ route('shop.index') }}">
                            {{-- Hidden inputs (match controller) --}}
                            <input type="hidden" name="brands" id="brandsInput" value="{{ $f_brands ?? '' }}">
                            <input type="hidden" name="categories" id="categoriesInput"
                                value="{{ $f_categories ?? '' }}">
                            <input type="hidden" name="min" id="minInput" value="{{ $min_price ?? 1 }}">
                            <input type="hidden" name="max" id="maxInput" value="{{ $max_price ?? 500 }}">
                            <input type="hidden" name="order" id="orderInput" value="{{ $order ?? -1 }}">
                            <input type="hidden" name="size" id="sizeInput" value="{{ $size ?? 12 }}">

                            {{-- CATEGORIES (unique id) --}}
                            <div class="widget widget-collapsible">
                                <h3 class="widget-title">
                                    <a data-bs-toggle="collapse" href="#categoriesCollapse" role="button"
                                        aria-expanded="true" aria-controls="categoriesCollapse">Categories</a>
                                </h3>

                                <div class="collapse show" id="categoriesCollapse">
                                    <div class="widget-body">
                                        <div class="filter-items filter-items-count">
                                            @foreach ($categories as $category)
                                                <div class="filter-item">
                                                    <div class="custom-control custom-checkbox">
                                                        {{-- NOTE: no `name` here: we serialize into the hidden input --}}
                                                        <input type="checkbox" name="categories[]" class="custom-control-input chk-category"
                                                            id="cat-{{ $category->id }}" value="{{ $category->id }}"
                                                            @if (in_array($category->id, explode(',', $f_categories ?? ''))) checked @endif>
                                                        <label class="custom-control-label"
                                                            for="cat-{{ $category->id }}">
                                                            {{ $category->name }}
                                                        </label>
                                                    </div>
                                                    <span class="item-count">{{ $category->products->count() }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- BRANDS (unique id) --}}


                            {{-- PRICE (unique id) --}}
                            <div class="widget widget-collapsible">
                                <h3 class="widget-title">
                                    <a data-toggle="collapse" href="#widget-5" role="button" aria-expanded="true"
                                        aria-controls="widget-5">
                                        Price
                                    </a>
                                </h3><!-- End .widget-title -->

                                <div class="collapse show" id="widget-5">
                                    <div class="widget-body">
                                        <div class="filter-price">
                                            <div class="filter-price-text">
                                                Price Range:
                                                <span id="filter-price-range"></span>
                                            </div><!-- End .filter-price-text -->

                                            <div id="price-slider"></divs><!-- End #price-slider -->
                                        </div><!-- End .filter-price -->
                                    </div><!-- End .widget-body -->
                                </div><!-- End .collapse -->
                            </div>

                        </form> {{-- end filterForm --}}
                    </div>
                </aside>
            </div>

            </div>
            <div class="sidebar-filter-overlay" id="sidebarFilterOverlay">
                <aside class="sidebar-shop sidebar-filter" id="sidebarShop">
                    <div class="sidebar-filter-wrapper">

                        <div class="widget widget-clean">
                            <label id="filterCloseBtn"><i class="icon-close"></i>Filters</label>
                            <a href="#" class="sidebar-filter-clear" id="filterClearBtn">Clean All</a>
                        </div>

                        <form id="filterForm" method="GET" action="{{ route('shop.index') }}">
                            {{-- Hidden inputs (match controller) --}}
                            <input type="hidden" name="brands" id="brandsInput" value="{{ $f_brands ?? '' }}">
                            <input type="hidden" name="categories" id="categoriesInput"
                                value="{{ $f_categories ?? '' }}">
                            <input type="hidden" name="min" id="minInput" value="{{ $min_price ?? 1 }}">
                            <input type="hidden" name="max" id="maxInput" value="{{ $max_price ?? 500 }}">
                            <input type="hidden" name="order" id="orderInput" value="{{ $order ?? -1 }}">
                            <input type="hidden" name="size" id="sizeInput" value="{{ $size ?? 12 }}">

                            {{-- CATEGORIES (unique id) --}}
                            <div class="widget widget-collapsible">
                                <h3 class="widget-title">
                                    <a data-bs-toggle="collapse" href="#categoriesCollapse" role="button"
                                        aria-expanded="true" aria-controls="categoriesCollapse">Categories</a>
                                </h3>

                                <div class="collapse show" id="categoriesCollapse">
                                    <div class="widget-body">
                                        <div class="filter-items filter-items-count">
                                            @foreach ($categories as $category)
                                                <div class="filter-item">
                                                    <div class="custom-control custom-checkbox">
                                                        {{-- NOTE: no `name` here: we serialize into the hidden input --}}
                                                        <input type="checkbox" class="custom-control-input chk-category"
                                                            id="cat-{{ $category->id }}" value="{{ $category->id }}"
                                                            @if (in_array($category->id, explode(',', $f_categories ?? ''))) checked @endif>
                                                        <label class="custom-control-label"
                                                            for="cat-{{ $category->id }}">
                                                            {{ $category->name }}
                                                        </label>
                                                    </div>
                                                    <span class="item-count">{{ $category->products->count() }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- BRANDS (unique id) --}}
                            <div class="widget widget-collapsible">
                                <h3 class="widget-title">
                                    <a data-bs-toggle="collapse" href="#brandsCollapse" role="button"
                                        aria-expanded="true" aria-controls="brandsCollapse">Brands</a>
                                </h3>

                                <div class="collapse show" id="brandsCollapse">
                                    <div class="widget-body">
                                        <div class="filter-items">
                                            @foreach ($brands as $brand)
                                                <div class="filter-item">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" name="brands[]" class="custom-control-input chk-brand"
                                                            id="brand-{{ $brand->id }}" value="{{ $brand->id }}"
                                                            @if (in_array($brand->id, explode(',', $f_brands ?? ''))) checked @endif>
                                                        <label class="custom-control-label"
                                                            for="brand-{{ $brand->id }}">
                                                            {{ $brand->name }}
                                                        </label>
                                                    </div>
                                                    <span class="item-count">{{ $brand->products_count }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- PRICE (unique id) --}}
                            <div class="accordion" id="price-filters">
                                <div class="accordion-item mb-4">
                                    <h5 class="accordion-header mb-2" id="accordion-heading-price">
                                        <button class="accordion-button p-0 border-0 fs-5 text-uppercase" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#priceCollapse"
                                            aria-expanded="true" aria-controls="priceCollapse">
                                            Price
                                        </button>
                                    </h5>

                                    <div id="priceCollapse" class="accordion-collapse collapse show border-0"
                                        aria-labelledby="accordion-heading-price" data-bs-parent="#price-filters">

                                        {{-- Keep existing slider markup used by your theme/plugin --}}
                                        <input class="price-range-slider" type="text" name="price_range"
                                            value="" data-slider-min="1" data-slider-max="500"
                                            data-slider-step="5"
                                            data-slider-value="[{{ $min_price }},{{ $max_price }}]"
                                            data-currency="₱" />

                                        <div class="price-range__info d-flex align-items-center mt-2">
                                            <div class="me-auto">
                                                <span class="text-secondary">Min Price: </span>
                                                <span class="price-range__min">₱{{ $min_price ?? 1 }}</span>
                                            </div>
                                            <div>
                                                <span class="text-secondary">Max Price: </span>
                                                <span class="price-range__max">₱{{ $max_price ?? 500 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form> {{-- end filterForm --}}
                    </div>

                </aside>
            </div>
        </section>
    </main>

    <form id="frmfilter" method="GET" action="{{ route('shop.index') }}">
        <input type="hidden" name="page" value="{{ $products->currentPage() }}">
        <input type="hidden" name="size" value="(($size))" id="size">
        <input type="hidden" name="order" id="order" value="{{ $order }}">
        <input type="hidden" name="brands" id="hdnBrands">
        <input type="hidden" name="categories" id="hdnCategories">
        <input type="hidden" name="min" id="hdnMinPrice" value="{{ $min_price }}">
        <input type="hidden" name="max" id="hdnMaxPrice" value="{{ $max_price }}">
    </form>
@endsection

@push('scripts')
    <script>
        $(function() {
            $("#pagesize").on("change", function() {
                $("#size").val($("#pagesize option:selected").val());
                $("#frmfilter").submit();
            });

            $("#orderby").on("change", function() {
                $("#order").val($("#orderby option:selected").val());
                $("#frmfilter").submit();
            });

            $("input[name='brands[]']").on("change", function() {
                var brands = [];
                $("input[name='brands[]']:checked").each(function() {
                    brands.push($(this).val());
                });

                $("#hdnBrands").val(brands.join(','));
                $("#frmfilter").submit();
            });

            $("input[name='categories[]']").on("change", function() {
                var categories = [];
                $("input[name='categories[]']:checked").each(function() {
                    categories.push($(this).val());
                });

                $("#hdnCategories").val(categories.join(','));
                $("#frmfilter").submit();
            });

            // $("[name='price_range']").on("change", function() {
            //     var min = $(this).val().split(',')[0];
            //     var max = $(this).val().split(',')[1];
            //     $("#hdnMinPrice").val(min);
            //     $("#hdnMaxPrice").val(max);
            //     $("#frmfilter").submit();
            // });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const overlay = document.getElementById('sidebarFilterOverlay');
            const sidebar = document.getElementById('sidebarShop');
            const toggler = document.querySelector('.sidebar-toggler');
            const filterForm = document.getElementById('filterForm');
            const categoriesInput = document.getElementById('categoriesInput');
            const brandsInput = document.getElementById('brandsInput');
            const minInput = document.getElementById('minInput');
            const maxInput = document.getElementById('maxInput');
            const clearBtn = document.getElementById('filterClearBtn');
            const closeBtn = document.getElementById('filterCloseBtn');

            // Open sidebar (hamburger)
            if (toggler) {
                toggler.addEventListener('click', function(e) {
                    e.preventDefault();
                    overlay.classList.add('active');
                    document.body.classList.add('overflow-hidden'); // optional: prevent scrolling
                });
            }

            // Clicking the dark overlay closes sidebar — but clicks inside sidebar should NOT bubble
            if (overlay) {
                overlay.addEventListener('click', function(e) {
                    if (e.target === overlay) {
                        overlay.classList.remove('active');
                        document.body.classList.remove('overflow-hidden');
                    }
                });
            }
            if (sidebar) {
                sidebar.addEventListener('click', function(e) {
                    e.stopPropagation(); // prevent overlay from seeing clicks inside
                });
            }

            // Close icon inside sidebar
            if (closeBtn) {
                closeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    overlay.classList.remove('active');
                    document.body.classList.remove('overflow-hidden');
                });
            }

            // Helper: read slider raw value "[min,max]" or "min,max"
            function parseSliderValue(raw) {
                if (!raw) return null;
                raw = String(raw).replace(/[\[\]\s"]/g, '');
                const parts = raw.split(',');
                if (parts.length >= 2) return [parseInt(parts[0] || 0), parseInt(parts[1] || 0)];
                return null;
            }

            // Submit filters (serialize checked boxes into comma-separated)
            function submitFilters() {
                const cats = Array.from(document.querySelectorAll('.chk-category:checked')).map(cb => cb.value)
                    .join(',');
                const brands = Array.from(document.querySelectorAll('.chk-brand:checked')).map(cb => cb.value).join(
                    ',');

                categoriesInput.value = cats;
                brandsInput.value = brands;

                filterForm.submit();
            }

            // Wire checkboxes
            document.querySelectorAll('.chk-category, .chk-brand').forEach(cb => {
                // prevent checkbox clicks from bubbling to overlay/collapse
                cb.addEventListener('click', function(e) {
                    e.stopPropagation();
                });

                // submit on change
                cb.addEventListener('change', function() {
                    submitFilters();
                });
            });

            // Price slider handling:
            const priceSlider = document.querySelector('.price-range-slider');
            if (priceSlider) {
                // If your theme/plugin triggers a 'change' event on the input, this will handle it.
                priceSlider.addEventListener('change', function(e) {
                    // plugin might update .value or data attribute
                    const raw = priceSlider.value || priceSlider.getAttribute('data-slider-value') ||
                        priceSlider.dataset.sliderValue;
                    const parsed = parseSliderValue(raw);
                    if (parsed) {
                        minInput.value = parsed[0];
                        maxInput.value = parsed[1];
                    }
                    filterForm.submit();
                });

                // Some slider plugins emit 'slideStop' or 'slide' events — attempt to bind them if available
                priceSlider.addEventListener('slideStop', function(e) {
                    const raw = priceSlider.value || priceSlider.getAttribute('data-slider-value') ||
                        priceSlider.dataset.sliderValue;
                    const parsed = parseSliderValue(raw);
                    if (parsed) {
                        minInput.value = parsed[0];
                        maxInput.value = parsed[1];
                    }
                    filterForm.submit();
                });
            }

            // Clean All — uncheck checkboxes and clear hidden inputs then submit
            if (clearBtn) {
                clearBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.querySelectorAll('.chk-category, .chk-brand').forEach(cb => cb.checked =
                        false);
                    categoriesInput.value = '';
                    brandsInput.value = '';
                    minInput.value = '{{ $min_price ?? 1 }}';
                    maxInput.value = '{{ $max_price ?? 500 }}';
                    filterForm.submit();
                });
            }
        });
        // Removed redundant immediate-submit handler. The page already wires change events
        // to serialize checked boxes into hidden inputs and submit via `submitFilters()`.
    </script>

    <style>
        /* minimal overlay/active styles if you don't have them already */
        .sidebar-filter-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 1050;
        }

        .sidebar-filter-overlay.active {
            display: block;
            background: rgba(0, 0, 0, 0.45);
        }

        .sidebar-shop {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 320px;
            background: #fff;
            overflow: auto;
            z-index: 1060;
        }
    </style>
@endpush
