<?php
namespace App\Http\Middleware;
use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticate;
use Closure;
class MyAuthenticate extends BaseAuthenticate
{
    public function handle($request, Closure $next, ...$guards)
    {
        \App\Support\DebugTimer::log(__FILE__, __LINE__, 'MyAuthenticate handle開始');
        $response = parent::handle($request, $next, ...$guards);
        \App\Support\DebugTimer::log(__FILE__, __LINE__, 'MyAuthenticate handle終了');
        return $response;
    }
}
