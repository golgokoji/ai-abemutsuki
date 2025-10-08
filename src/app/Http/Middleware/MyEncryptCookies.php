<?php
namespace App\Http\Middleware;
use Illuminate\Cookie\Middleware\EncryptCookies as BaseEncryptCookies;
use Closure;
class MyEncryptCookies extends BaseEncryptCookies
{
    public function handle($request, Closure $next)
    {
        \App\Support\DebugTimer::log(__FILE__, __LINE__, 'MyEncryptCookies handle開始');
        $response = parent::handle($request, $next);
        \App\Support\DebugTimer::log(__FILE__, __LINE__, 'MyEncryptCookies handle終了');
        return $response;
    }
}
