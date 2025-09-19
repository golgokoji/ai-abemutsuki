<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Webhooks\PayzWebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| このファイルでは API 用のルートを定義します。
| RouteServiceProvider により自動で "api" プレフィックスが付与されます。
| 例: /api/ping, /api/webhooks/payz
|
*/

// 確認用エンドポイント（疎通確認用）
Route::get('/ping', function (Request $request) {
    return response()->json([
        'pong' => true,
        'ip'   => $request->ip(),
    ]);
});

// Payz webhook 受信用
Route::post('/webhooks/payz', PayzWebhookController::class);
