<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbelaboUserSetting extends Model
{
    protected $table = 'abelabo_user_settings';
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'tel',
    ];
    public $timestamps = true;
}
