<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FishAudioService
{
    public function generate(string $text, string $fileName, array $overrides = []): array
    {
        $cfg      = config('fishaudio');
        $apiKey   = env('FISH_AUDIO_API_KEY');
        $endpoint = rtrim($cfg['api_base'] ?? 'https://api.fish.audio', '/') . '/v1/tts';

        $body = [
            "model"        => $cfg['model'] ?? "s1",
            "text"         => $text,
            "reference_id" => $cfg['reference_id'] ?? null,
            "format"       => $cfg['format'] ?? "mp3",
            "latency"      => $cfg['latency'] ?? "normal",
            "mp3_bitrate"  => $cfg['mp3_bitrate'] ?? 128,
            "opus_bitrate" => $cfg['opus_bitrate'] ?? 32,
            "normalize"    => $cfg['normalize'] ?? true,
            "chunk_length" => $cfg['chunk_length'] ?? 200,
            "prosody"      => $cfg['prosody'] ?? ["volume" => 0],
            "sample_rate"  => null,
            "temperature"  => $cfg['temperature'] ?? 0.7,
            "top_p"        => $cfg['top_p'] ?? 0.7,
        ];

        $body = array_replace_recursive($body, $overrides);

        // --- ここでcurlコマンドを生成 ---
        $json = json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $curl = "curl -X POST \"" . $endpoint . "\" \\\n"
              . "  -H \"Authorization: Bearer " . $apiKey . "\" \\\n"
              . "  -H \"Content-Type: application/json\" \\\n"
              . "  -d '" . $json . "'";

        Log::info("[FishAudio cURL] " . $curl);

        // --- 実際のリクエスト ---
        $resp = Http::withHeaders([
                "Authorization" => "Bearer " . $apiKey,
                "Content-Type"  => "application/json",
                "Accept"        => "audio/mpeg",
            ])->post($endpoint, $body);

        if (!$resp->ok()) {
            return ["success" => false, "path" => null, "error" => $resp->body()];
        }

    $relPath = "voices/" . $fileName;
    Storage::disk("s3")->put($relPath, $resp->body());

    $s3Url = rtrim(config('filesystems.disks.s3.url'), '/') . '/' . ltrim($relPath, '/');

    return ["success" => true, "path" => $relPath, "url" => $s3Url, "error" => null];
    }
}
