<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewReaction extends Model
{
    protected $fillable = ['review_id', 'user_id', 'reaction'];

    public function review()
    {
        return $this->belongsTo(\App\Models\Review::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
