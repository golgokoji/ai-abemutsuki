<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RequestTimer
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
        $start = microtime(true);
        $response = $next($request);
        $end = microtime(true);
        $ms = ($end - $start) * 1000;
        $ms = number_format($ms, 2);
        $method = $request->getMethod();
        $path = $request->getPathInfo();
        Log::info("Request time: {$ms}ms [{$method} {$path}]");
        return $response;
    }
}
