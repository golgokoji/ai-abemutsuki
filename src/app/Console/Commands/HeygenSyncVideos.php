<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\AvatarVideo;

class HeygenSyncVideos extends Command
{
    /**
     * 使い方:
     *  php artisan heygen:sync-videos --limit=50 --voice=xxxxx --dry-run
     *
     * 環境変数:
     *  HEYGEN_API_KEY=...
     *  HEYGEN_STALLED_MINUTES=10   // processingがこの分数以上なら同期対象（既定10）
     */
    protected $signature = 'heygen:sync-videos
        {--voice= : 対象のvoice_idを指定（単体/系統復旧）}
        {--limit=50 : 一括復旧時の最大件数}
        {--dry-run : 取得可否のみ表示（実保存しない）}
        {--minutes= : processing経過分数（未指定時は HEYGEN_STALLED_MINUTES or 10）}';

    protected $description = 'HeyGenでcompletedになった動画を回収しS3へ保存、status=succeededに更新します（processing滞留の復旧用）。';

    public function handle(): int
    {
        $apiKey = config('heygen.api_key') ?? env('HEYGEN_API_KEY');
        if (empty($apiKey)) {
            $this->error('HEYGEN_API_KEY が未設定です（config/heygen.php または .env）。');
            return self::FAILURE;
        }

        // ★ 重複実行の防止（3分ロック、最大5秒待ち）
        $lock = Cache::lock('heygen:sync-videos', 180);
        if (! $lock->block(5)) {
            $this->warn('別プロセスが実行中のため終了します。');
            return self::SUCCESS;
        }

        try {
            $voiceId = (string)($this->option('voice') ?? '');
            $limit   = (int)($this->option('limit') ?? 50);
            $dryRun  = (bool)$this->option('dry-run');

            $minutesOpt = $this->option('minutes');
            $stalledMin = is_numeric($minutesOpt)
                ? (int)$minutesOpt
                : (int)(env('HEYGEN_STALLED_MINUTES', 10));

            $cutoff = now()->subMinutes(max(1, $stalledMin));
            $maxAge = now()->subHours(24);

            // ★ 10分（既定）以上更新がない processing かつ 24時間以内のみ対象
            $q = AvatarVideo::query()
                ->where('provider', 'heygen')
                ->where('status', 'processing')
                ->whereNotNull('video_id')
                ->where('updated_at', '<=', $cutoff)
                ->where('updated_at', '>=', $maxAge)
                ->orderByDesc('id');

            if ($voiceId !== '') {
                $q->where('voice_id', $voiceId);
            }

            $targets = $q->limit($limit)->get();

            if ($targets->isEmpty()) {
                $this->info("対象レコードが見つかりませんでした（updated_at <= {$cutoff}, status=processing）。");
                return self::SUCCESS;
            }

            $this->info(sprintf(
                '対象: %d 件（dry-run: %s, stalled>=%d分, cutoff=%s）',
                $targets->count(),
                $dryRun ? 'ON' : 'OFF',
                $stalledMin,
                $cutoff->toDateTimeString()
            ));

            foreach ($targets as $video) {
                // すでに成功確定ならスキップ（冪等性）
                if ($video->status === 'succeeded' && !empty($video->file_url)) {
                    $this->line("id={$video->id} は既にsucceededのためスキップ");
                    continue;
                }

                $this->line("voice_id={$video->voice_id} video_id={$video->video_id} を確認中…");

                // ステータス取得
                $statusRes = Http::withHeaders([
                        'X-Api-Key' => $apiKey,
                        'Accept'    => 'application/json',
                    ])
                    ->timeout(30)
                    ->get('https://api.heygen.com/v1/video_status.get', [
                        'video_id' => $video->video_id,
                    ]);

                if (!$statusRes->ok()) {
                    $this->warn("  → ステータス取得失敗 (HTTP {$statusRes->status()})");
                    Log::warning('heygen:sync-videos status http error', [
                        'video_pk' => $video->id,
                        'video_id' => $video->video_id,
                        'http'     => $statusRes->status(),
                        'body'     => $statusRes->body(),
                    ]);
                    continue;
                }

                $data   = $statusRes->json();
                $status = data_get($data, 'data.status', 'unknown');
                $this->line("  → HeyGen status: {$status}");

                if ($status === 'completed') {
                    // URLの優先順位（仕様差分に備えて両対応）
                    $videoUrl = data_get($data, 'data.video_url') ?: data_get($data, 'data.download_url');
                    if (!$videoUrl) {
                        $this->error('  → video_url が取得できませんでした。failedに更新します。');
                        if (!$dryRun) {
                            $video->update([
                                'status' => 'failed',
                                'provider_response' => $data,
                            ]);
                        }
                        continue;
                    }

                    if ($dryRun) {
                        $this->info('  → 取得可能（dry-runのため保存せず）');
                        continue;
                    }

                    // ダウンロード（簡易実装: 一括取得）。大容量対応が必要ならstream化をご検討ください。
                    // --- ストリーミングダウンロード＆S3アップロード（大容量対応） ---
                    $tmpDir = storage_path('app/tmp');
                    if (!is_dir($tmpDir)) {
                        mkdir($tmpDir, 0775, true);
                    }
                    $tmpPath = $tmpDir . '/' . (($video->video_id ?? Str::random(16)) . '.mp4');
                    $s3path  = 'videos/' . Str::random(16) . '.mp4';
                    $publicUrl = null;
                    try {
                        $resp = Http::withOptions([
                            'stream'       => true,
                            'sink'         => $tmpPath,
                            'timeout'      => 0,
                            'read_timeout' => 300,
                        ])->get($videoUrl);
                        if (!$resp->ok() || !file_exists($tmpPath) || filesize($tmpPath) === 0) {
                            throw new \RuntimeException('download http ' . $resp->status() . ' or file missing: ' . $tmpPath);
                        }
                        $contentType = $resp->header('Content-Type');
                        if ($contentType && strpos($contentType, 'video') === false) {
                            throw new \RuntimeException('unexpected content-type: ' . $contentType);
                        }
                        $stream = fopen($tmpPath, 'r');
                        Storage::disk('s3')->put($s3path, $stream, ['ContentType' => 'video/mp4']);
                        fclose($stream);
                        $publicUrl = Storage::disk('s3')->url($s3path);

                        $duration = data_get($data, 'data.duration');

                        $video->update([
                            'status'             => 'succeeded',
                            'file_url'           => $publicUrl,
                            'provider_response'  => $data,
                            'duration'           => $duration,
                        ]);

                        // クレジット消費（定義がある場合のみ）
                        try {
                            if ($duration && $video->user) {
                                $video->user->consumeCreditForVideo((int)$duration, $video->id);
                            }
                        } catch (\Throwable $creditEx) {
                            Log::error('heygen:sync-videos credit consume error', [
                                'video_pk' => $video->id,
                                'video_id' => $video->video_id,
                                'error'    => $creditEx->getMessage(),
                            ]);
                        }

                        $this->info("  → 復旧成功: {$publicUrl}");
                        Log::info('heygen:sync-videos recovered', [
                            'video_pk' => $video->id,
                            'video_id' => $video->video_id,
                            'voice_id' => $video->voice_id,
                            'url'      => $publicUrl,
                        ]);
                    } catch (\Throwable $e) {
                        $this->error('  → S3保存/取得でエラー: ' . $e->getMessage());
                        Log::error('heygen:sync-videos s3/download error', [
                            'video_pk' => $video->id,
                            'video_id' => $video->video_id,
                            'error'    => $e->getMessage(),
                            'tmpPath'  => $tmpPath,
                        ]);
                    } finally {
                        if (file_exists($tmpPath)) {
                            @unlink($tmpPath);
                        }
                    }

                    continue;
                }

                if (in_array($status, ['failed', 'canceled'], true)) {
                    $this->warn('  → HeyGen側が failed/canceled。failedに更新します。');
                    if (!$dryRun) {
                        $video->update([
                            'status' => 'failed',
                            'provider_response' => $data,
                        ]);
                    }
                    continue;
                }

                // まだ処理中
                $this->line('  → まだ処理中のためスキップ');
            }

            return self::SUCCESS;

        } finally {
            optional($lock)->release();
        }
    }
}
