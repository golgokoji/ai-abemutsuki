<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SessionTimer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // セッション初期化直後に計測ログ
        $request->session();
        \App\Support\DebugTimer::log(__FILE__, __LINE__, 'SessionTimer: session初期化直後');
        return $next($request);
    }
}
