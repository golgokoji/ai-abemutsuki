<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    /**
     * ---------------------------
     * リレーション
     * ---------------------------
     */
    public function creditHistories()
    {
        return $this->hasMany(CreditHistory::class, 'user_id');
    }

    public function voices()
    {
        return $this->hasMany(Voice::class);
    }

    public function avatarVideos()
    {
        return $this->hasMany(AvatarVideo::class);
    }

    /**
     * ---------------------------
     * クレジット関連メソッド
     * ---------------------------
     */

    // 残高取得
    public function getCreditBalance(): int
    {
        return $this->credit_balance ?? 0;
    }

    // 消費処理（動画作成時）
    public function consumeCreditForVideo(int $duration, ?int $videoId = null): bool
    {
        $consume = (int) ceil($duration / 30);
        $actualConsume = min($consume, $this->getCreditBalance());

        $this->credit_balance -= $actualConsume;
        $this->save();

        CreditHistory::create([
            'user_id'  => $this->id,
            'amount'   => 0,
            'system'   => 'consume',
            'credit'   => -$actualConsume,
            'video_id' => $videoId,
            'note'     => '動画作成によるクレジット消費',
            'order_id' => null,
        ]);

        return true;
    }

    /**
     * ---------------------------
     * Filament アクセス制御
     * ---------------------------
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // 管理者のみアクセス可能（通常ユーザーは拒否）
        // すべてのユーザーを一時的に許可したい場合は「return true;」に変更
        return (bool) $this->is_admin;
    }

    /**
     * ---------------------------
     * 通知
     * ---------------------------
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }

    /**
     * ---------------------------
     * モデル設定
     * ---------------------------
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'credit_balance',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }
}
