<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, $product_id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        // ✅ Check if user purchased this product with a successful order
        $hasPurchased = \App\Models\Order::where('user_id', auth()->id())
            ->where('status', 'delivered') // adjust to your success status
            ->whereHas('orderItems', function ($query) use ($product_id) {
                $query->where('product_id', $product_id);
            })
            ->exists();

        if (!$hasPurchased) {
            return back()->with('error', 'You can only review products you have purchased.');
        }

        // ✅ Prevent duplicate reviews
        $existingReview = \App\Models\Review::where('user_id', auth()->id())
            ->where('product_id', $product_id)
            ->first();

        if ($existingReview) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        $imagePath = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = 'uploads/reviews/' . $imageName;
            $image->move(public_path('uploads/reviews'), $imageName);
        }

        \App\Models\Review::create([
            'product_id' => $product_id,
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
            'image' => $imagePath,
        ]);

        return back()->with('success', 'Review submitted!');
    }
    public function edit(Review $review)
    {
        if (auth()->id() !== $review->user_id) {
            abort(403);
        }
        return view('review-edit', compact('review'));
    }

    public function update(Request $request, Review $review)
    {
        if (auth()->id() !== $review->user_id) {
            abort(403);
        }

        $validated = $request->validate([
            'comment' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'image' => 'nullable|image|max:2048',
        ]);

        // Handle optional new image upload
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($review->image && file_exists(public_path($review->image))) {
                unlink(public_path($review->image));
            }

            // Store new image
            $newImage = $request->file('image');
            $newImageName = time() . '_' . uniqid() . '.' . $newImage->getClientOriginalExtension();
            $newImagePath = 'uploads/reviews/' . $newImageName;
            $newImage->move(public_path('uploads/reviews'), $newImageName);

            $validated['image'] = $newImagePath;
        }

        $review->update($validated);

        return redirect()
            ->route('shop.product.details', ['product_slug' => $review->product->slug])
            ->with('success', 'Review updated!');
    }

    public function destroy(Review $review)
    {
        if (auth()->id() !== $review->user_id) {
            abort(403);
        }

        $review->delete();

        return redirect()->back()->with('success', 'Review deleted successfully!');
    }

}
