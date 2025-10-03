<?php

namespace App\Jobs;

use App\Models\Voice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\FishAudioService;
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

        $script = $voice->script;
        $fileName = "voice_{$voice->id}.mp3";

        try {
            $service = new FishAudioService();
            $result = $service->generate($script->text, $fileName);

            if (!$result['success']) {
                $voice->update([
                    'status'            => 'failed',
                    'provider_response' => ['error' => $result['error']],
                ]);
                return;
            }

            $relPath = $result['path'];
            $publicUrl = $result['url'] ?? null;

            $voice->update([
                'status'     => 'succeeded',
                'file_path'  => $relPath,
                'public_url' => $publicUrl,
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
