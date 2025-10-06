<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponLog extends Model
{
    protected $table = 'coupon_logs';
    protected $fillable = [
        'code', 'email', 'credit', 'user_id', 'ip', 'user_agent'
    ];
}
