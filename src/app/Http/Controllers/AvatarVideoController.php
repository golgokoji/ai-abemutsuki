<?php

namespace App\Http\Controllers;

use App\Models\AvatarVideo;
use Illuminate\Http\Request;

class AvatarVideoController extends Controller
{
    /**
     * アバター動画一覧
     */
    public function index()
    {
        $user = auth()->user();

        $videos = AvatarVideo::with('voice.script')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(20);

        return view('avatar_videos.index', compact('videos'));
    }
}
