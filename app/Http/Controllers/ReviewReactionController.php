<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ReviewReaction;
use Illuminate\Http\Request;

class ReviewReactionController extends Controller
{
    public function react(Request $request, $review_id)
    {
        $request->validate([
            'reaction' => 'required|in:helpful,unhelpful',
        ]);

        $review = Review::findOrFail($review_id);

        // Prevent reacting to own review
        if ($review->user_id == auth()->id()) {
            return back()->with('error', 'You cannot react to your own review.');
        }

        // Save or update reaction
        ReviewReaction::updateOrCreate(
            [
                'review_id' => $review_id,
                'user_id' => auth()->id(),
            ],
            [
                'reaction' => $request->reaction,
            ]
        );

        return back()->with('success', 'Your reaction has been recorded.');
    }
}
