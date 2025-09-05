<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'script_id',
        'status',
        'provider',
        'file_path',
        'public_url',
        'provider_response',
    ];

    protected $casts = [
        'provider_response' => 'array',
    ];

    public function script()
    {
        return $this->belongsTo(Script::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
