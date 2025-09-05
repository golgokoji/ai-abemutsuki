<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HeygenTestController extends Controller
{
    public function create(Request $request)
    {
        $audioUrl = $request->get('audio_url'); // ブラウザから渡す or 固定でテストもOK

        $payload = [
            'avatar_id'  => env('HEYGEN_AVATAR_ID'),
            'audio_url'  => $audioUrl,
            'caption'    => false,
            'background' => 'transparent',
        ];

        $resp = Http::withHeaders([
            'X-Api-Key'    => env('HEYGEN_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.heygen.com/v1/video/create', $payload);

        return response($resp->body(), $resp->status())
    ->header('Content-Type', 'application/json');

        
    }
}
