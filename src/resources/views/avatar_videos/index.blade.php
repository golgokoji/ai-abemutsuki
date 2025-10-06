<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            アバター動画一覧
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- タブ風ナビ（必要なければ削除） --}}
            <div class="flex gap-6 mb-6">
                <a href="{{ route('voices.index') }}"
                   class="px-4 py-2 rounded bg-gray-100 text-gray-700 hover:bg-indigo-50">音声一覧</a>
                <a href="{{ route('avatar_videos.index') }}"
                   class="px-4 py-2 rounded bg-indigo-100 text-indigo-700 font-bold">動画一覧</a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($videos->isEmpty())
                        <p class="text-gray-500">まだ動画がありません。</p>
                    @else
                        <ul class="space-y-2">
                            @foreach($videos as $vd)
                                <li class="p-3 border rounded">
                                    <div>
                                        <span class="inline-block text-xs px-2 py-1 rounded bg-gray-100 mr-2">#{{ $vd->id }}</span>
                                        <span class="font-medium">
                                            {{ $vd->title ?? 'VideoID: '.($vd->video_id ?? '(無題)') }}
                                        </span>
                                        <span class="ml-2 text-sm text-gray-500">[{{ $vd->status }}]</span>
                                        @if(!empty($vd->voice_id))
                                            <span class="ml-2 text-xs text-gray-400">VoiceID: {{ $vd->voice_id }}</span>
                                        @endif
                                    </div>

                                    <div class="mt-1 text-xs text-gray-600">
                                        音声: {{ optional(optional($vd->voice)->script)->title ?? '(無題)' }}
                                    </div>

                                    <div class="flex gap-3 mt-2 justify-end">
                                        @php
                                            use Illuminate\Support\Facades\Storage;
                                            $playUrl = $vd->file_url
                                                ?? $vd->public_url
                                                ?? (!empty($vd->file_path) ? Storage::url($vd->file_path) : null);
                                        @endphp
                                        @if($playUrl)
                                            <a href="{{ $playUrl }}" target="_blank"
                                               class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm">
                                                再生
                                            </a>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        <div class="mt-6">
                            {{ $videos->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
