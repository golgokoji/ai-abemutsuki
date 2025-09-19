<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateVoiceJob;
use App\Models\Script;
use App\Models\Voice;
use Illuminate\Http\Request;

class TtsController extends Controller
{
    public function form(Request $request)
    {
        $voices = Voice::with('script')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->limit(20)
            ->get();
        $videos = \App\Models\AvatarVideo::with('voice')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->limit(20)
            ->get();
        return view('tts.form', compact('voices', 'videos'));
    }

    public function submit(Request $request)
    {
        $data = $request->validate([
            'text' => ['required','string','max:20000'],
            'title'=> ['nullable','string','max:255'],
        ]);

        // 1) 台本を保存

        $script = Script::create([
            'user_id' => $request->user()->id,
            'text'    => $data['text'],
            'title'   => $data['title'] ?? null,
        ]);

        // 2) 音声レコードを queued で作成（user_idもセット）
        $voice = Voice::create([
            'user_id'   => $request->user()->id,
            'script_id' => $script->id,
            'status'    => 'queued',
            'provider'  => 'elevenlabs',
        ]);

        // 3) 非同期ジョブを発行
        GenerateVoiceJob::dispatch($voice->id);

        return redirect()->route('tts.form')
            ->with('status', '音声生成をキューに投入しました。（数十秒後に更新して確認）');
    }
}
