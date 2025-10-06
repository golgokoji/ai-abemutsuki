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
        $user = auth()->user();
        $voices = Voice::with('script')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(20);
        return view('voices.index', compact('voices'));
    }
}
