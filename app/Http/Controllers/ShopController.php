<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $size = (int) $request->query('size') ? $request->query('size') : 8;
        $o_column = '';
        $o_order = '';
        $order = (int) $request->query('order') ? $request->query('order') : -1;
        $f_brands = $request->query('brands');
        $f_categories = $request->query('categories');
    $min_price = $request->query('min') ? $request->query('min') : 1;
    // sensible default max price: use products max values or 500 as fallback
    $computedMax = max((float) Product::max('regular_price'), (float) Product::max('sale_price')) ?: 500;
    $max_price = $request->query('max') ? $request->query('max') : $computedMax;
        switch ($order) {
            case 1:
                $o_column = 'created_at';
                $o_order = 'DESC';
                break;
            case 2:
                $o_column = 'created_at';
                $o_order = 'ASC';
                break;
            case 3:
                $o_column = 'sale_price';
                $o_order = 'ASC';
                break;
            case 4:
                $o_column = 'sale_price';
                $o_order = 'DESC';
                break;
            default:
                $o_column = 'id';
                $o_order = 'DESC';
        }
        $brands = Brand::withCount('products')->orderBy('name', 'ASC')->get();

        $categories = Category::orderBy('name', 'ASC')->get();

        $products = Product::when($f_brands, function ($query) use ($f_brands) {
            $query->whereIn('brand_id', explode(',', $f_brands));
        })
            ->when($f_categories, function ($query) use ($f_categories) {
                $query->whereIn('category_id', explode(',', $f_categories));
            })
            ->where(function ($query) use ($min_price, $max_price) {
                $query->whereBetween('regular_price', [$min_price, $max_price])->orWhereBetween('sale_price', [$min_price, $max_price]);
            })
            ->orderBy($o_column, $o_order)
            ->paginate($size);

        return view('shop', compact('products', 'size', 'order', 'brands', 'f_brands', 'categories', 'f_categories', 'min_price', 'max_price'));
    }

    public function product_details(Request $request, $product_slug)
    {
        $product = Product::where('slug', $product_slug)->firstOrFail();

        $selectedRating = $request->has('rating') ? (int) $request->rating : null;

        $reviewsQuery = $product->reviews()->latest();

        if ($selectedRating) {
            $reviewsQuery->where('rating', $selectedRating);
        }

        $userReview = null;
        if (auth()->check()) {
            $userReview = (clone $reviewsQuery)->where('user_id', auth()->id())->first();
        }

        // Exclude user review for pagination
        $paginatedReviewsQuery = (clone $reviewsQuery)->when($userReview, fn($q) => $q->where('id', '!=', $userReview->id));

        $reviews = $paginatedReviewsQuery->paginate(5)->withQueryString();

        // Merge user review at the top if exists
        if ($userReview) {
            $reviews->prepend($userReview);
        }

        // Count reviews for each star
        $reviewCounts = $product->reviews()->selectRaw('rating, COUNT(*) as count')->groupBy('rating')->pluck('count', 'rating');

        $rproducts = Product::where('id', '!=', $product->id)->inRandomOrder()->take(8)->get();

        return view('details', compact('product', 'rproducts', 'reviews', 'selectedRating', 'reviewCounts'));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        // Fetch random products, excluding the one being viewed
        $rproducts = Product::where('id', '!=', $product->id)->inRandomOrder()->take(8)->get();

        return view('shop.product-details', compact('product', 'rproducts'));
    }
}
