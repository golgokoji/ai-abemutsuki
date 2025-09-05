<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Script extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'text'];

    public function voices()
    {
        return $this->hasMany(Voice::class);
    }
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
