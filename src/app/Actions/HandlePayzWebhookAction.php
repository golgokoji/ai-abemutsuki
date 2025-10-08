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
            // status: 'purchased' の場合はユーザー特定せずpending保存のみ
            $userId = null;
            // status: 'purchased' の場合、purchase_uidをキーにpending作成
            if (($data['status'] ?? '') === 'purchased') {
                // 冪等性チェック（purchase_uidで）
                if (\App\Models\PayzPendingGrant::where('purchase_uid', $data['purchase_uid'])->exists()) {
                    Log::info('payz pending skipped (duplicate purchase_uid)', [
                        'purchase_uid' => $data['purchase_uid']]);
                    return;
                }
                $productUid = $data['product_uid'] ?? null;
                $plan = null;
                if ($productUid) {
                    $plan = \App\Models\PaymentPlan::where('product_uid', $productUid)
                        ->where('is_active', true)
                        ->first();
                }
                if (!$plan) {
                    Log::warning('payz pending skipped (no matching plan)', [
                        'purchase_uid' => $data['purchase_uid'],
                        'product_uid' => $productUid,
                    ]);
                    return;
                }
                \App\Models\PayzPendingGrant::create([
                    'purchase_uid' => $data['purchase_uid'],
                    'product_uid' => $data['product_uid'] ?? null,
                    'payment_email' => $data['email'] ?? null,
                    'credit' => $plan->credit,
                    'amount' => $plan->amount,
                    'expires_at' => now()->addDay(),
                    'payload' => json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
                ]);
                Log::info('payz pending created', [
                    'purchase_uid' => $data['purchase_uid'],
                    'credit' => $plan->credit,
                    'amount' => $plan->amount,
                ]);
                return;
            }

            // status: 'subscribing' の場合のみユーザー特定してcredit付与
            if (($data['status'] ?? '') === 'subscribing') {
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
                DB::transaction(function () use ($userId, $data) {
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
            }
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
