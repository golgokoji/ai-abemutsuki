<?php
namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class ResetPasswordNotification extends BaseResetPassword
{
    /**
     * パスワードリセット通知メール（標準文面サンプル）
     */
    public function toMail($notifiable)
    {
        $systemName = env('APP_NAME', 'AIあべむつき');
        return (new MailMessage)
            ->subject($systemName . ' | パスワード再設定のご案内')
            ->line('このメールは、あなたのアカウントのパスワード再設定リクエストに基づき送信されています。')
            ->action('パスワードを再設定する', $this->resetUrl($notifiable))
            ->line('このパスワード再設定リンクの有効期限は ' . config('auth.passwords.' . config('auth.defaults.passwords') . '.expire') . ' 分です。')
            ->line('もしパスワード再設定を希望されていない場合は、このメールは破棄してください。')
            ->salutation('よろしくお願いいたします');
    }
}
