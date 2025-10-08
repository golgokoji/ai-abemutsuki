<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentPlan extends Model
{
    protected $fillable = [
        'name',
        'product_uid',
        'amount',
        'credit',
        'is_active',
    ];
}
