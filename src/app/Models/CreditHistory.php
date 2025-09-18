<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditHistory extends Model
{
    protected $table = 'credit_histories';
    protected $fillable = [
        'user_id',
        'order_id',
        'amount',
        'credit',
        'system',
        'granted_at',
        'note',
    ];
    public $timestamps = true;
}
