<?php

namespace App\Jobs;

use App\Models\Voice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GenerateVoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 音声レコードのID
     *
     * @var int
     */
    public int $voiceId;

    /**
     * リトライ回数（必要に応じて）
     */
    public $tries = 3;

    /**
     * timeout 秒（ElevenLabs の応答待ちが長い場合は調整可）
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(int $voiceId)
    {
        $this->voiceId = $voiceId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
    $voice = Voice::find($this->voiceId);
        if (!$voice) {
            return;
        }

    $voice->update(['status' => 'processing']);

        $script  = $voice->script;
        $apiKey  = config('services.elevenlabs.key') ?? env('ELEVENLABS_API_KEY');
        $voiceId = env('ELEVENLABS_VOICE_ID');
        $modelId = env('ELEVENLABS_MODEL_ID', 'eleven_multilingual_v2');

        // S3保存先パス
        $relPath = "voices/voice_{$voice->id}.mp3";

        try {
            $url  = "https://api.elevenlabs.io/v1/text-to-speech/{$voiceId}";
            $resp = Http::withHeaders([
                'xi-api-key'   => $apiKey,
                'Accept'       => 'audio/mpeg',
                'Content-Type' => 'application/json',
            ])->post($url, [
                'model_id'       => $modelId,
                'text'           => $script->text,
                'voice_settings' => [
                    'stability'        => 0.5,
                    'similarity_boost' => 0.75,
                ],
            ]);

            if (!$resp->ok()) {
                $voice->update([
                    'status'             => 'failed',
                    'provider_response'  => ['code' => $resp->status(), 'body' => $resp->json()],
                ]);
                return;
            }

            // mp3 をS3に保存
            // Storage::disk('s3')->put($relPath, $resp->body(), ['visibility' => 'public']);
            Storage::disk('s3')->put($relPath, $resp->body());

            $voice->update([
                'status'     => 'succeeded',
                'file_path'  => $relPath,
                'public_url' => rtrim(config('filesystems.disks.s3.url'), '/') . '/' . ltrim($relPath, '/'),
            ]);
        } catch (\Throwable $e) {
            $voice->update([
                'status'            => 'failed',
                'provider_response' => ['exception' => $e->getMessage()],
            ]);
            throw $e;
        }
    }
}
