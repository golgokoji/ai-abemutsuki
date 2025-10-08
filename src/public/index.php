<?php


use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}




// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';


// Bootstrap Laravel and handle the request...
/** @var Application $app */


$app = require_once __DIR__.'/../bootstrap/app.php';

// 計測開始ログ（Application生成後に移動）
if (file_exists(__DIR__.'/../app/Support/DebugTimer.php')) {
    require_once __DIR__.'/../app/Support/DebugTimer.php';
    \App\Support\DebugTimer::log(__FILE__, __LINE__, 'index.php開始');
}


$app->handleRequest(Request::capture());

// 計測終了ログ（handleRequest後に移動）
if (file_exists(__DIR__.'/../app/Support/DebugTimer.php') && class_exists('\App\Support\DebugTimer')) {
    \App\Support\DebugTimer::log(__FILE__, __LINE__, 'index.php終了');
}
