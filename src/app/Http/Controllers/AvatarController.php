<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateAvatarJob;
use App\Models\Voice;

class AvatarController extends Controller
{
    public function generate(Voice $voice)
    {
        // 音声が成功しているものだけ許可
        if ($voice->status !== 'succeeded') {
            return back()->with('status', '音声が未生成です。まずは音声をsucceededまで完了してください。');
        }

        GenerateAvatarJob::dispatch($voice->id);

        return back()->with('status', 'アバター動画生成をキューに投入しました。少し後に再読込してください。');
    }
}
