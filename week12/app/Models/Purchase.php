<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'currency',
        'status',
        'stripe_session_id',
        'stripe_payment_intent',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}