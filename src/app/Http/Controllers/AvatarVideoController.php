<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AvatarVideo;

class AvatarVideoController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $videos = AvatarVideo::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(20);
        return view('avatar_videos.index', compact('videos'));
    }
}