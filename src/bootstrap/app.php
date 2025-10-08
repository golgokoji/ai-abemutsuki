<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function ($schedule) {
        // 毎日午前3時にインフォトップ売上取込ジョブを実行
        $schedule->job(new \App\Jobs\ImportInfotopSalesJob)->dailyAt('03:00');
    })
    ->withMiddleware(function (Middleware $middleware): void {
    // TrustProxies
    $middleware->append(\App\Http\Middleware\TrustProxies::class);
    // SessionTimer（セッション初期化直後の計測ログ）
    $middleware->appendToGroup('web', \App\Http\Middleware\SessionTimer::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
