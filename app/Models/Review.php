<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 * @property \App\Models\Product $product
 * @property \App\Models\User $user
 */

class Review extends Model
{
    protected $fillable = ['product_id', 'user_id', 'rating', 'comment', 'image'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reactions()
    {
        return $this->hasMany(\App\Models\ReviewReaction::class);
    }

    public function helpfulCount()
    {
        return $this->reactions()->where('reaction', 'helpful')->count();
    }

    public function unhelpfulCount()
    {
        return $this->reactions()->where('reaction', 'unhelpful')->count();
    }

    public function reactedByUser($reactionType)
    {
        return $this->reactions()
            ->where('user_id', auth()->id())
            ->where('reaction', $reactionType)
            ->exists();
    }

}
