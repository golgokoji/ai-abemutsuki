<?php

namespace App\Http\Controllers;

use App\Models\Voice;
use Illuminate\Http\Request;

class VoiceController extends Controller
{
    /**
     * 音声一覧
     */
    public function index()
    {
        $voices = Voice::with('script')->latest()->paginate(5); // 1ページ5件
        return view('voices.index', compact('voices'));
    }
}
