<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Voice;
use App\Models\AvatarVideo;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        

        $voices = Voice::with('script')
            ->where('user_id', Auth::id())
            ->latest()
            ->limit(20)
            ->get();
        $videos = AvatarVideo::where('user_id', Auth::id())
            ->latest()
            ->limit(20)
            ->get();
        return view('dashboard', compact('voices', 'videos'));
    }
}
