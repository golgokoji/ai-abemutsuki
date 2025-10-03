<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public function creditHistories()
    {
        return $this->hasMany(CreditHistory::class, 'user_id');
    }

    /**
     * クレジット残高取得
     */
    public function getCreditBalance(): int
    {
        return $this->credit_balance ?? 0;
    }

    /**
     * クレジット消費（動画作成時）
     * @param int $duration 動画秒数
     * @param int|null $videoId 動画ID
     * @return bool 成功時true
     */
    public function consumeCreditForVideo(int $duration, ?int $videoId = null): bool
    {
        $consume = (int) ceil($duration / 30);
        $actualConsume = min($consume, $this->getCreditBalance());
        $this->credit_balance -= $actualConsume;
        $this->save();
        \App\Models\CreditHistory::create([
            'user_id' => $this->id,
            'amount'  => 0,
            'system'  => 'consume',
            'credit'  => -$actualConsume,
            'video_id'=> $videoId,
            'note'    => '動画作成によるクレジット消費',
            'order_id'=> null, // ←追加
        ]);
        return true;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'credit_balance'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function voices()
    {
        return $this->hasMany(Voice::class);
    }

    public function avatarVideos()
    {
        return $this->hasMany(AvatarVideo::class);
    }


        public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->is_admin; // 管理者のみ /admin へ
    }

    public function sendPasswordResetNotification($token)
{
    $this->notify(new \App\Notifications\ResetPasswordNotification($token));
}
}
