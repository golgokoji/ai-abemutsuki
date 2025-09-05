<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvatarVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'voice_id','status','provider','video_id','file_url','provider_response'
    ];

    protected $casts = [
        'provider_response' => 'array',
    ];

    public function voice()
    {
        return $this->belongsTo(\App\Models\Voice::class);
    }
}
