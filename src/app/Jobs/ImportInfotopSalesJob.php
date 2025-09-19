<?php
namespace App\Jobs;

use App\Services\InfotopCreditGrantService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
/**
 * ImportInfotopSalesJob
 *
 * Googleスプレッドシート(salesシート)からインフォトップ売上データを取得し、
 * クレジット付与処理を行うJob。
 *
 * - 呼び出し元:
 *   - 管理画面 (手動ボタン)
 *   - スケジューラ (bootstrap/app.php 内で dailyAt などで登録)
 *   - Artisan や dispatchSync() による直接実行も可能
 *
 * - 運用:
 *   - order_id は DB の UNIQUE 制約により二重付与が防止される
 *   - 付与ポイントは .env (ABELABO_BONUS_CREDIT) に従う
 *   - 特別プラン (9800円未満) はスキップ
 *
 * - 実運用フロー:
 *   1. 本番サーバーの cron に以下を登録（1分ごと）
 *        * * * * * php /var/www/html/artisan schedule:run >> /dev/null 2>&1
 *   2. Laravel スケジューラが毎日 03:00 にこの Job を dispatch
 *   3. 管理画面からの手動実行でも即時動作確認が可能
 */

class ImportInfotopSalesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $service = new InfotopCreditGrantService();
        $summary = $service->run();
        Log::info('Infotop sales import summary', $summary);
    }
}
