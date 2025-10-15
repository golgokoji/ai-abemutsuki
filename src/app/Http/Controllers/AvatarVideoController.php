<?php

namespace App\Http\Controllers;

use App\Models\AvatarVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AvatarVideoController extends Controller
{

    /**
     * 動画削除（レコード＋S3ファイル）
     */
    public function destroy($id)
    {
    $user = Auth::user();
        $video = AvatarVideo::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        // S3ファイル削除（file_urlがS3の場合のみ）
        if ($video->file_url) {
            $s3Url = $video->file_url;
            $disk = Storage::disk('s3');
            $baseUrl = rtrim(config('filesystems.disks.s3.url'), '/');
            $path = ltrim(str_replace($baseUrl, '', $s3Url), '/');
            if ($disk->exists($path)) {
                $disk->delete($path);
            }
        }

        $video->delete();

        return redirect()->route('avatar_videos.index')->with('status', '動画を削除しました');
    }
    /**
     * アバター動画一覧
     */
    public function index()
    {
    $user = Auth::user();

        $videos = AvatarVideo::with('voice.script')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(20);

        return view('avatar_videos.index', compact('videos'));
    }
}
