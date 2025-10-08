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
    Log::info($msg);
    }
}
