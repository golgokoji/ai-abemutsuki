<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditHistory extends Model
{
    protected $table = 'credit_histories';
    protected $fillable = [
        'user_id',
        'amount',
        'credit',
        'system',
        'video_id',
        'order_id',
        'note',
    ];
    public $timestamps = true;
}
