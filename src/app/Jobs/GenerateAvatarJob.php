<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;

use App\Models\AvatarVideo;
use App\Models\Voice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GenerateAvatarJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $voiceId;
    public $tries = 3;
    public $timeout = 1200;

    public function __construct(int $voiceId)
    {
        $this->voiceId = $voiceId;

        Log::info('GenerateAvatarJob __construct', ['voice_id' => $voiceId]);
        Log::info('GenerateAvatarJob __construct param check', [
            'voiceId' => $voiceId,
            'type' => gettype($voiceId),
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('GenerateAvatarJob failed', [
            'voice_id' => $this->voiceId,
            'exception' => $exception->getMessage(),
        ]);
    }


    public function handle()
    {
        Log::info('GenerateAvatarJob3 handle entered', ['voice_id' => $this->voiceId]);
        $voice = Voice::find($this->voiceId);
        Log::info('GenerateAvatarJob start', ['voice_id' => $this->voiceId]);

        if (!$voice) return;

        // クレジット残高チェック
        $user = $voice->user;
        if (!$user || $user->credit_balance <= 0) {
            Log::warning('GenerateAvatarJob: クレジット残高不足のためfail扱い', [
                'voice_id' => $this->voiceId,
                'user_id' => $user ? $user->id : null,
                'credit_balance' => $user ? $user->credit_balance : null,
            ]);
            throw new \Exception('クレジット残高が不足しています');
        }

        // 音声URLを決定（絶対URLになるように）
        $audioUrl = $voice->public_url
            ?: url(Storage::url(str_replace('public/', '', $voice->file_path)));

        // HeyGen設定値はconfigから取得
        $cfg = config('heygen');
        $apiKey = $cfg['api_key'] ?? null;
        if (empty($apiKey)) {
            AvatarVideo::where('voice_id', $voice->id)
                ->latest()->first()?->update([
                    'status' => 'failed',
                    'provider_response' => ['error' => 'HEYGEN_API_KEYが未設定です（config/heygen.php）'],
                ]);
            return;
        }

        $avatarId = $cfg['avatar_id'] ?? null;
        $dimension = $cfg['dimension'] ?? ['width' => 1280, 'height' => 720];
        // v2 API: payload構造
        $payload = [
            "video_inputs" => [[
                "character" => [
                    "type"        => "avatar",
                    "avatar_id"   => $avatarId,
                    "avatar_style" => "normal",
                    "emotion"      => "neutral",   // 表情を穏やかに
                    "gesture"      => "none",   // ← ここで手振りを抑える
                ],
                "voice" => [
                    "type"      => "audio",
                    "audio_url" => $audioUrl,
                ],
                "background" => [
                    "type"  => "color",
                    "value" => "#000000",
                ],
            ]],
            "dimension" => $dimension,
        ];


        // 既存レコード取得（voice_id+provider）
        $video = AvatarVideo::where('voice_id', $voice->id)
            ->where('provider', 'heygen')
            ->latest()->first();

        // statusがprocessingならスキップ
        if ($video && $video->status === 'processing') {
            Log::info('動画生成Job: 処理中のためスキップ', ['voice_id' => $voice->id]);
            return;
        }

        // 新規作成（または再生成）
        $video = AvatarVideo::create([
            'user_id'  => $voice->user_id,
            'voice_id' => $voice->id,
            'status'   => 'processing',
            'provider' => 'heygen',
            'provider_response' => null,
        ]);

        // 1) 動画生成リクエスト（v2 API）
        $res = Http::withHeaders([
            'X-Api-Key'     => $apiKey,
            'Content-Type'  => 'application/json',
        ])->post('https://api.heygen.com/v2/video/generate', $payload);

        if (!$res->ok() || empty($res['data']['video_id'])) {
            $video->update([
                'status' => 'failed',
                'provider_response' => $res->json(),
            ]);
            return;
        }

        $videoId = $res['data']['video_id'];
        $video->update([
            'video_id' => $videoId,
            'provider_response' => $res->json(),
        ]);

        // 2) ポーリングで完了待ち
        $maxTries = 80;
        $interval = 15;
        $lastStatusData = null;
        for ($i = 1; $i <= $maxTries; $i++) {
            sleep($interval);
            $statusRes = Http::withHeaders(['X-Api-Key' => $apiKey])
                ->get('https://api.heygen.com/v1/video_status.get', ['video_id' => $videoId]);

            if (!$statusRes->ok()) {
                continue;
            }

            $data   = $statusRes->json();
            $lastStatusData = $data;
            $status = $data['data']['status'] ?? 'unknown';
            if ($status === 'completed') {
                $videoUrl = $data['data']['video_url'] ?? ($data['data']['download_url'] ?? null);
                if ($videoUrl) {
                    // --- sinkによるダウンロード＆S3アップロード（大容量対応） ---
                    $tmpDir = storage_path('app/tmp');
                    if (!is_dir($tmpDir)) {
                        mkdir($tmpDir, 0775, true);
                    }
                    $tmpPath = $tmpDir . '/' . (($video->video_id ?? bin2hex(random_bytes(8))) . '.mp4');
                    $random = bin2hex(random_bytes(8));
                    $s3path = "videos/{$random}.mp4";
                    $publicUrl = null;
                    try {
                        $resp = Http::withOptions([
                            'sink'           => $tmpPath,
                            'timeout'        => 0,
                            'read_timeout'   => 300,
                            'allow_redirects'=> ['track_redirects' => true],
                        ])->get($videoUrl);
                        if (!$resp->ok() || !file_exists($tmpPath) || filesize($tmpPath) === 0) {
                            throw new \RuntimeException('file missing or empty after sink: status=' . $resp->status() . ' path=' . $tmpPath);
                        }
                        $contentType = $resp->header('Content-Type');
                        if ($contentType && strpos($contentType, 'video') === false) {
                            throw new \RuntimeException('unexpected content-type: ' . $contentType);
                        }
                        $stream = fopen($tmpPath, 'r');
                        Storage::disk('s3')->put($s3path, $stream, ['ContentType' => 'video/mp4']);
                        fclose($stream);
                        $publicUrl = rtrim(config('filesystems.disks.s3.url'), '/') . '/' . ltrim($s3path, '/');
                        // 動画のduration取得（秒）
                        $duration = $data['data']['duration'] ?? null;
                        $video->update([
                            'status' => 'succeeded',
                            'file_url' => $publicUrl,
                            'provider_response' => $data,
                            'duration' => $duration,
                        ]);

                        // クレジット消費処理
                        $user = $video->user;
                        if ($duration && $user) {
                            $user->consumeCreditForVideo((int)$duration, $video->id);
                        }
                        return;
                    } catch (\Throwable $e) {
                        Log::error('GenerateAvatarJob video streaming error', [
                            'video_pk' => $video->id,
                            'video_id' => $video->video_id,
                            'error'    => $e->getMessage(),
                            'tmpPath'  => $tmpPath,
                        ]);
                        $video->update([
                            'status' => 'failed',
                            'provider_response' => $data,
                        ]);
                        return;
                    } finally {
                        if (file_exists($tmpPath)) {
                            @unlink($tmpPath);
                        }
                    }
                }
                $video->update([
                    'status' => 'failed',
                    'provider_response' => $data,
                ]);
                return;
            }
            if (in_array($status, ['failed', 'canceled'], true)) {
                $video->update([
                    'status' => 'failed',
                    'provider_response' => $data,
                ]);
                return;
            }
        }
        // タイムアウト: 最後のレスポンスも保存
        $video->update([
            'status' => 'failed',
            'provider_response' => [
                'error' => 'timeout',
                'last_status' => $lastStatusData,
            ],
        ]);
    }
}
