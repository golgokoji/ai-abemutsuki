<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Artisanコマンドの登録
     */
    protected $commands = [
        \App\Console\Commands\HeygenSyncVideos::class,
    ];

    /**
     * スケジュール登録
     */
    protected function schedule(Schedule $schedule)
    {
    // 10分ごとにheygen:sync-videosを実行
    $schedule->command('heygen:sync-videos')->everyTenMinutes();
    }

    /**
     * コマンドのロード
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
