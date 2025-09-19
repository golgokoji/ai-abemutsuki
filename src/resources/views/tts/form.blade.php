
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            台本 → 音声生成（ElevenLabs）
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- 音声生成フォームカード --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('status'))
                        <div class="mb-4 text-green-600">{{ session('status') }}</div>
                    @endif
                    <form method="post" action="{{ route('tts.submit') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block font-medium">タイトル（任意）</label>
                            <input type="text" name="title" class="mt-1 block w-full border border-gray-300 focus:border-indigo-500 rounded px-3 py-2" value="{{ old('title') }}">
                        </div>
                        <div>
                            <label class="block font-medium">台本テキスト</label>
                            <textarea name="text" rows="8" class="mt-1 block w-full border border-gray-300 focus:border-indigo-500 rounded px-3 py-2">{{ old('text') }}</textarea>
                            @error('text')<div class="text-red-600 mt-1">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">音声生成を依頼（キュー投入）</button>
                    </form>
                </div>
            </div>

            {{-- 最新の音声カード --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-semibold text-lg mb-4">最新の音声（直近20件）</h3>
                    @if($voices->isEmpty())
                        <p class="text-gray-500">作成された音声はまだありません。</p>
                    @else
                        <ul class="space-y-2">
                            @foreach($voices as $v)
                                <li class="flex items-center justify-between p-3 border rounded">
                                    <div>
                                        <span class="inline-block text-xs px-2 py-1 rounded bg-gray-100 mr-2">#{{ $v->id }}</span>
                                        <span class="font-medium">{{ optional($v->script)->title ?? '(無題)' }}</span>
                                        <span class="ml-2 text-sm text-gray-500">[{{ $v->status }}]</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        @if($v->public_url)
                                            <a class="text-indigo-600 underline" target="_blank" href="{{ $v->public_url }}">再生</a>
                                        @elseif($v->file_path)
                                            <a class="text-indigo-600 underline" target="_blank" href="{{ \Illuminate\Support\Facades\Storage::url($v->file_path) }}">再生</a>
                                        @endif
                                        @if($v->status === 'succeeded')
                                            <form method="post" action="{{ route('avatar.generate', ['voice' => $v->id]) }}">
                                                @csrf
                                                <button class="text-sm px-3 py-1 border rounded hover:bg-gray-50">
                                                    この音声でアバター動画生成
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            {{-- 最新のアバター動画カード --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-semibold text-lg mb-4">最新のアバター動画（直近20件）</h3>
                    @if($videos->isEmpty())
                        <p class="text-gray-500">まだありません。音声を生成後、「アバター動画生成」を実行してください。</p>
                    @else
                        <ul class="space-y-2">
                            @foreach($videos as $vd)
                                <li class="flex items-center justify-between p-3 border rounded">
                                    <div>
                                        <span class="inline-block text-xs px-2 py-1 rounded bg-gray-100 mr-2">#{{ $vd->id }}</span>
                                        <span class="font-medium">VideoID: {{ $vd->video_id ?? '-' }}</span>
                                        <span class="ml-2 text-sm text-gray-500">[{{ $vd->status }}]</span>
                                        <span class="ml-2 text-xs text-gray-400">VoiceID: {{ $vd->voice_id }}</span>
                                    </div>
                                    <div>
                                        @if($vd->file_url)
                                            <a class="text-indigo-600 underline" target="_blank" href="{{ $vd->file_url }}">再生</a>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>