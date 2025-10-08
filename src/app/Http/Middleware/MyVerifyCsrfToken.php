<?php
namespace App\Http\Middleware;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifyCsrfToken;
use Closure;
class MyVerifyCsrfToken extends BaseVerifyCsrfToken
{
    public function handle($request, Closure $next)
    {
        \App\Support\DebugTimer::log(__FILE__, __LINE__, 'MyVerifyCsrfToken handle開始');
        $response = parent::handle($request, $next);
        \App\Support\DebugTimer::log(__FILE__, __LINE__, 'MyVerifyCsrfToken handle終了');
        return $response;
    }
}
