<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponInitialCredit extends Model
{
    protected $table = 'coupon_initial_credits';
    protected $fillable = [
        'code',
        'credit',
        'is_active',
        'valid_from',
        'valid_until'
    ];
    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = $value ?: null;
    }
}
