<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Requests\PayzWebhookRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Payz Webhook Receiver
 *
 * このコントローラーは、Payz 決済の webhook 通知（JSON, Content-Type: application/json）を受け取り、
 * バリデーション・冪等性ガード（重複防止）・ログ記録を行った上で、後続のビジネスロジックに渡します。
 *
 * ## 想定されるリクエスト例（JSON）
 * 
 * webhookで来るデータ
 * {
 *     "received_at": "2025-10-06T21:14:36+09:00",
 *     "remote_addr": "44.213.72.139",
 *     "method": "POST",
 *     "uri": "/pages/webhook/webhook.php",
 *     "query": [],
 *     "headers": {
 *         "Content-Type": "application/json",
 *         "Accept-Encoding": "gzip;q=1.0,deflate;q=0.6,identity;q=0.3",
 *         "User-Agent": "Ruby",
 *         "Connection": "close",
 *         "Host": "abe-labo.biz",
 *         "Content-Length": "498"
 *     },
 *     "body_raw": "{\"purchase_uid\":\"P_NOU2NKJQ\",\"product_uid\":\"pd_fuxs2lgeyrzl5jat\",\"user_uid\":\"ur_nhkp4j9qvp4hyqom\",\"email\":\"golgokoji@gmail.com\",\"status\":\"purchased\",\"name_last\":\"ueda\",\"name_first\":\"koji\",\"kana_last\":null,\"kana_first\":null,\"prefecture\":null,\"city\":null,\"address1\":null,\"address2\":null,\"zip\":null,\"tel\":\"09058116238\",\"fax\":null,\"dob\":null,\"client_ip\":\"220.100.20.85\",\"client_ua\":\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36\"}",
 *     "json": {
 *         "purchase_uid": "P_NOU2NKJQ",
 *         "product_uid": "pd_fuxs2lgeyrzl5jat",
 *         "user_uid": "ur_nhkp4j9qvp4hyqom",
 *         "email": "golgokoji@gmail.com",
 *         "status": "purchased",
 *         "name_last": "ueda",
 *         "name_first": "koji",
 *         "kana_last": null,
 *         "kana_first": null,
 *         "prefecture": null,
 *         "city": null,
 *         "address1": null,
 *         "address2": null,
 *         "zip": null,
 *         "tel": "09058116238",
 *         "fax": null,
 *         "dob": null,
 *         "client_ip": "220.100.20.85",
 *         "client_ua": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36"
 *     }
 * }
 * 
 * このウェブフックが送られると、payzから購入者あてに
 * クレジット付与URLがメールで送られる
 * /charge?purchase_uid=xxxxx&email=yyyy
 * 
 * 以降はChargeControllerでクレジット付与処理を行う
 * 決済時のEMAILとログインユーザーのEMAILは異なる場合がある
 * purchase_uid=xxxxx&email=yyyy
 * このパラメータはwebhookのデータが正しいかチェックするために使う
 * 
 */
class PayzWebhookController
{
    /**
     * Handle Payz webhook POST (application/json).
     *
     * @param  \App\Http\Requests\PayzWebhookRequest  $request
     * @return \Illuminate\Http\Response  204 No Content（常に本文なし）
     *
    * @example curl -X POST http://localhost:8080/api/webhooks/payz \
    *   -H "Content-Type: application/json" \
    *   -d '{
    *     "purchase_uid": "P_NOU2NKJQ",
    *     "product_uid": "pd_fuxs2lgeyrzl5jat",
    *     "user_uid": "ur_nhkp4j9qvp4hyqom",
    *     "email": "golgokoji@gmail.com",
    *     "status": "purchased",
    *     "name_last": "ueda",
    *     "name_first": "koji",
    *     "kana_last": null,
    *     "kana_first": null,
    *     "prefecture": null,
    *     "city": null,
    *     "address1": null,
    *     "address2": null,
    *     "zip": null,
    *     "tel": "09058116238",
    *     "fax": null,
    *     "dob": null,
    *     "client_ip": "220.100.20.85",
    *     "client_ua": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36"
    *   }'
    */
    public function __invoke(PayzWebhookRequest $request): Response
    {
        // 1) Validate & sanitize input
        $data = $request->validated();

        // 受信データ全体をinfoログに出力
        Log::info('payz webhook received', [
            'raw' => $request->all(),
            'validated' => $data,
        ]);

        // 2) statusによってロックキー分岐
        $lockKey = ($data['status'] ?? '') === 'purchased'
            ? 'payz:' . ($data['purchase_uid'] ?? 'unknown')
            : 'payz:' . ($data['subscription_uid'] ?? 'unknown');
        $lock = Cache::lock($lockKey, 10);
        if (! $lock->get()) {
            Log::warning('payz webhook duplicate (locked)', [
                'lock_key' => $lockKey,
                'purchase_uid' => $data['purchase_uid'] ?? null,
                'subscription_uid' => $data['subscription_uid'] ?? null,
            ]);
            return response()->noContent(); // 204
        }

        try {
            Log::info('payz webhook validated', [
                'lock_key' => $lockKey,
                'purchase_uid' => $data['purchase_uid'] ?? null,
                'subscription_uid' => $data['subscription_uid'] ?? null,
                'membership_uid'   => $data['membership_uid'] ?? null,
                'email'            => $data['email'] ?? null,
                'status'           => $data['status'] ?? null,
            ]);

            // 3) アプリ本体処理（後続のAction/Serviceで実装）
            \App\Actions\HandlePayzWebhookAction::run($data);

            // レスポンスは常に本文なしの 204（再送や冪等性の都合上、成功時も内容は返さない）
            return response()->noContent();

        } finally {
            // 4) Release lock
            optional($lock)->release();
        }
    }
}
