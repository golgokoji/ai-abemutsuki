<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ダッシュボード
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- アクションカード --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('tts.form') }}"
                           class="block p-4 border rounded-lg hover:bg-gray-50">
                            <div class="font-bold">台本 → 音声生成</div>
                            <div class="text-sm text-gray-500 mt-1">新規で音声を作る</div>
                        </a>

                        <a href="{{ route('profile.edit') }}"
                           class="block p-4 border rounded-lg hover:bg-gray-50">
                            <div class="font-bold">プロフィール / パスワード設定</div>
                            <div class="text-sm text-gray-500 mt-1">HeyGen APIキー など</div>
                        </a>

                        <form method="post" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button class="w-full p-4 border rounded-lg text-left hover:bg-gray-50">
                                <div class="font-bold">ログアウト</div>
                                <div class="text-sm text-gray-500 mt-1">セッションを終了</div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- 最新の音声 --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-semibold text-lg mb-4">最新の音声（直近20件）</h3>
                    @if($voices->isEmpty())
                        <p class="text-gray-500">まだありません。<a href="{{ route('tts.form') }}" class="text-indigo-600 underline">こちら</a>から作成できます。</p>
                    @else
                        <ul class="space-y-2">
                            @foreach($voices as $v)
                                <div>テスト: {{ $v->id }}</div>
                                <li class="p-3 border rounded">
                                    <div>
                                        <span class="inline-block text-xs px-2 py-1 rounded bg-gray-100 mr-2">#{{ $v->id }}</span>
                                        <span class="font-medium">{{ displayTitleOrText($v->script) }}</span>
                                        <span class="ml-2 text-sm text-gray-500">[{{ $v->status }}]</span>
                                    </div>
                                    <div class="flex gap-3 mt-2 justify-end">
                                        @if($v->public_url)
                                            <form action="{{ $v->public_url }}" method="get" target="_blank" style="display:inline;">
                                                <button type="submit" class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm">再生</button>
                                            </form>
                                        @elseif($v->file_path)
                                            <form action="{{ \Illuminate\Support\Facades\Storage::url($v->file_path) }}" method="get" target="_blank" style="display:inline;">
                                                <button type="submit" class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm">再生</button>
                                            </form>
                                        @endif
                                        @if($v->status === 'succeeded')
                                            <form method="post" action="{{ route('avatar.generate', ['voice' => $v->id]) }}" style="display:inline;">
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

            {{-- 最新のアバター動画 --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-semibold text-lg mb-4">最新のアバター動画（直近20件）</h3>
                    @if($videos->isEmpty())
                        <p class="text-gray-500">まだありません。音声を生成後、「アバター動画生成」を実行してください。</p>
                    @else
                        <ul class="space-y-2">
                            @foreach($videos as $vd)
                                <li class="p-3 border rounded">
                                    <div>
                                        <span class="inline-block text-xs px-2 py-1 rounded bg-gray-100 mr-2">#{{ $vd->id }}</span>
                                        <span class="font-medium">VideoID: {{ $vd->video_id ?? '-' }}</span>
                                        <span class="ml-2 text-sm text-gray-500">[{{ $vd->status }}]</span>
                                    </div>
                                    <div class="mt-1 text-xs text-gray-600">
                                        音声: {{ optional(optional($vd->voice)->script)->title ?? '(無題)' }}
                                    </div>
                                    <div class="flex gap-3 mt-2 justify-end">
                                        @if($vd->file_url)
                                            <form action="{{ $vd->file_url }}" method="get" target="_blank" style="display:inline;">
                                                <button type="submit" class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm">再生</button>
                                            </form>
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
