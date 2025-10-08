<?php
return [
    // HeyGen APIキー
    'api_key' => env('HEYGEN_API_KEY'),

    // アバターID
    'avatar_id' => env('HEYGEN_AVATAR_ID'),

    // 動画サイズ（デフォルト: 1280x720）
    'dimension' => [
        'width' => env('HEYGEN_DIMENSION_WIDTH', 1280),
        'height' => env('HEYGEN_DIMENSION_HEIGHT', 720),
    ],
];
