<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayzPendingGrant extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_uid',
        'product_uid',
        'payment_email',
        'amount',
        'credit',
        'claimed_user_id',
        'claimed_at',
        'expires_at',
        'payload',
    ];

    protected $casts = [
        'claimed_at' => 'datetime',
        'expires_at' => 'datetime',
        'payload' => 'array',
    ];
}
