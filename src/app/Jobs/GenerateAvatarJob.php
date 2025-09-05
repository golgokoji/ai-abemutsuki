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
    public $timeout = 180;

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

        // 音声URLを決定（絶対URLになるように）
        $audioUrl = $voice->public_url
            ?: url(Storage::url(str_replace('public/', '', $voice->file_path)));

        // ユーザーのHeyGen APIキーを取得
        $apiKey = optional($voice->script->user)->heygen_api_key;
        if (empty($apiKey)) {
            AvatarVideo::where('voice_id', $voice->id)
                ->latest()->first()?->update([
                    'status' => 'failed',
                    'provider_response' => ['error' => 'ユーザーのHeyGen APIキーが未設定です'],
                ]);
            return;
        }

        $avatarId = env('HEYGEN_AVATAR_ID');
        // v2 API: payload構造
        $payload = [
            "video_inputs" => [[
                "character" => [
                    "type"        => "avatar",
                    "avatar_id"   => $avatarId,
                    "avatar_style" => "normal",
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
            "dimension" => ["width" => 1280, "height" => 720],
        ];

        // DBレコード作成（user_idもセット）
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
        $maxTries = 60;
        $interval = 30;
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
                    // 動画をダウンロードしてS3に保存
                    $bin = Http::get($videoUrl)->body();
                    $random = bin2hex(random_bytes(8));
                    $s3path = "videos/{$random}.mp4";
                    // Storage::disk('s3')->put($s3path, $bin, ['visibility' => 'public']);
                    Storage::disk('s3')->put($s3path, $bin, [
                        'ContentType' => 'video/mp4',
                    ]);
                    $publicUrl = rtrim(config('filesystems.disks.s3.url'), '/') . '/' . ltrim($s3path, '/');
                    $video->update([
                        'status' => 'succeeded',
                        'file_url' => $publicUrl,
                        'provider_response' => $data,
                    ]);
                    return;
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
