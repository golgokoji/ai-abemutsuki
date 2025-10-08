<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        // ...既存のグローバルミドルウェア
        \App\Http\Middleware\RequestTimer::class,
    ];

    // ...他の設定（$middlewareGroups, $routeMiddleware など）
}
