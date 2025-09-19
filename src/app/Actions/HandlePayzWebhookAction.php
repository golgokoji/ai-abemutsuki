<?php
namespace App\Actions;

use App\Models\AbelaboUserSetting;
use App\Models\CreditHistory;
use App\Support\Phone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Payz Webhookを処理し、該当ユーザーにクレジット付与するAction
 */
class HandlePayzWebhookAction
{
    /**
     * Payz Webhookデータを処理
     *
     * @param array $data
     * @return void
     */
    public static function run(array $data): void
    {
        try {
            // ユーザー特定
            $userSetting = self::findUser($data);
            if (!$userSetting) {
                Log::info('payz credit skipped (user not found)', [
                    'email' => $data['email'] ?? null,
                    'tel' => $data['tel'] ?? null,
                    'name_last' => $data['name_last'] ?? null,
                    'name_first' => $data['name_first'] ?? null,
                ]);
                return;
            }
            $userId = $userSetting->user_id;
            // 冪等性チェック（order_idにsubscription_uidを使う）
            if (CreditHistory::where('order_id', $data['subscription_uid'])->exists()) {
                Log::info('payz credit skipped (duplicate order_id)', [
                    'order_id' => $data['subscription_uid']]);
                return;
            }
            // statusがsubscribingのみ付与
            if (($data['status'] ?? '') !== 'subscribing') {
                Log::info('payz credit skipped (status not subscribing)', [
                    'subscription_uid' => $data['subscription_uid'], 'status' => $data['status'] ?? null]);
                return;
            }



            DB::transaction(function () use ($userId, $data) {
                // AMOUNTには10000を指定
                $amount = 10000;
                $bonusCredit = env('ABELABO_BONUS_CREDIT', 1);
                $history = CreditHistory::create([
                    'user_id' => $userId,
                    'order_id' => $data['subscription_uid'],
                    'credit' => $bonusCredit,
                    'amount' => $amount,
                    'system' => 'payz_abelabo',
                    'note' => 'payz:' . $data['subscription_uid'],
                    'granted_at' => now(),
                ]);
                // ユーザーのcredit_balanceを加算
                $user = \App\Models\User::find($userId);
                if ($user) {
                    $user->increment('credit_balance', $bonusCredit);
                }
                Log::info('payz credit granted', [
                    'user_id' => $userId,
                    'order_id' => $data['subscription_uid'],
                    'credit' => $bonusCredit,
                    'amount' => 0,
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('payz credit error', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
        }
    }

    /**
     * abelabo_user_settingsからユーザー特定
     * @param array $data
     * @return AbelaboUserSetting|null
     */
    private static function findUser(array $data): ?AbelaboUserSetting
    {
        // メールアドレス（大文字小文字無視）と電話番号（完全一致）のみで判定
        if (!empty($data['email']) && !empty($data['tel'])) {
            $user = AbelaboUserSetting::whereRaw('LOWER(email) = ?', [Str::lower($data['email'])])
                ->where('tel', $data['tel'])
                ->first();
            if ($user) return $user;
        }
        return null;
    }

    /**
     * 氏名の正規化（全角半角・空白トリム）
     * @param string $name
     * @return string
     */
    private static function normalizeName(string $name): string
    {
        return trim(mb_convert_kana($name, 's'));
    }
}
