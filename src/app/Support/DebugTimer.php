<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;

class DebugTimer
{
    /**
     * ログにファイル・行番号・経過ミリ秒を出力
     * @param string $file
     * @param int $line
     * @param string|null $label
     */
    public static function log($file, $line, $label = null)
    {
        $start = $GLOBALS['__TIMESTAMP__'] ?? microtime(true);
        $now = microtime(true);
        $ms = ($now - $start) * 1000;
        $ms = number_format($ms, 2);
        $msg = "【処理計測】{$label} - {$file}:{$line} ここまでに {$ms}ミリ秒かかりました";
        try {
            Log::info($msg);
        } catch (\Throwable $e) {
            $logPath = base_path('storage/logs/laravel.log');
            $timestamp = date('[Y-m-d H:i:s]');
            file_put_contents($logPath, $timestamp . ' ' . $msg . "\n", FILE_APPEND);
        }
    }
}
