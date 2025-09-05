<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class HeygenTest extends Command
{
    protected $signature = 'heygen:test {--interval=5} {--max=60}';
    protected $description = 'Fixed audio URL -> HeyGen generate -> poll until completed';

    public function handle(): int
    {
        $apiKey   = env('HEYGEN_API_KEY');
        $avatarId = env('HEYGEN_AVATAR_ID');
        $audioUrl = env('FIXED_AUDIO_URL', 'https://ai-abemutsuki.s3.ap-southeast-2.amazonaws.com/voices/sample.mp3');

        if (!$apiKey || !$avatarId) {
            $this->error('HEYGEN_API_KEY / HEYGEN_AVATAR_ID を .env に設定してください。');
            return self::FAILURE;
        }

        $this->info('✔ HeyGen へ動画生成を発注します（固定オーディオURL）...');
        $payload = [
            "video_inputs" => [[
                "character" => [
                    "type"        => "avatar",
                    "avatar_id"   => $avatarId,
                    "avatar_style"=> "normal",
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
            "dimension" => [ "width" => 1280, "height" => 720 ],
        ];

        $res = Http::withHeaders([
            'X-Api-Key'     => $apiKey,
            'Content-Type'  => 'application/json',
        ])->post('https://api.heygen.com/v2/video/generate', $payload);

        if (!$res->ok() || empty($res['data']['video_id'])) {
            $this->error('✖ 発注失敗');
            $this->line(json_encode($res->json(), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
            return self::FAILURE;
        }

        $videoId = $res['data']['video_id'];
        $this->info("✔ 発注成功 video_id={$videoId}");
        $this->line('… 生成完了までポーリングします');

        $interval = (int)$this->option('interval'); // 秒
        $maxTries = (int)$this->option('max');      // 試行回数

        for ($i = 1; $i <= $maxTries; $i++) {
            sleep($interval);

            $statusRes = Http::withHeaders(['X-Api-Key' => $apiKey])
                ->get('https://api.heygen.com/v1/video_status.get', ['video_id' => $videoId]);

            if (!$statusRes->ok()) {
                $this->warn("試行 {$i}/{$maxTries}: ステータス取得失敗（HTTP {$statusRes->status()}）");
                continue;
            }

            $data   = $statusRes->json();
            $status = $data['data']['status'] ?? 'unknown';
            $this->line("試行 {$i}/{$maxTries}: status={$status}");

            if ($status === 'completed') {
                $videoUrl = $data['data']['video_url'] ?? ($data['data']['download_url'] ?? null);
                if ($videoUrl) {
                    $this->newLine();
                    $this->info('✔ 完了しました。ダウンロードURL（期限あり）：');
                    $this->line($videoUrl);
                    return self::SUCCESS;
                }
                $this->warn('完了しましたが URL が見つかりません。レスポンスを表示します：');
                $this->line(json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
                return self::SUCCESS;
            }

            if (in_array($status, ['failed', 'canceled'], true)) {
                $this->error("✖ 生成が {$status} になりました。詳細：");
                $this->line(json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
                return self::FAILURE;
            }
        }

        $this->warn('⚠ タイムアウトしました。後ほど再度 status をご確認ください。');
        $this->line("video_id: {$videoId}");
        return self::FAILURE;
    }
}
