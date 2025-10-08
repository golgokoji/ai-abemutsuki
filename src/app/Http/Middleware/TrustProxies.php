<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies = '*'; // ← すべてのリバースプロキシを信頼

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR
                        | Request::HEADER_X_FORWARDED_HOST
                        | Request::HEADER_X_FORWARDED_PORT
                        | Request::HEADER_X_FORWARDED_PROTO;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        \App\Support\DebugTimer::log(__FILE__, __LINE__, 'TrustProxies handle開始');
        $response = parent::handle($request, $next);
        \App\Support\DebugTimer::log(__FILE__, __LINE__, 'TrustProxies handle終了');
        return $response;
    }
}
