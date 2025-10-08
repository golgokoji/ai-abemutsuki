
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            音声一覧
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="flex gap-6 mb-6">
                <a href="{{ route('voices.index') }}" class="px-4 py-2 rounded bg-indigo-100 text-indigo-700 font-bold">音声一覧</a>
                <a href="{{ route('avatar_videos.index') }}" class="px-4 py-2 rounded bg-gray-100 text-gray-700 hover:bg-indigo-50">動画一覧</a>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($voices->isEmpty())
                        <p class="text-gray-500">まだ音声がありません。</p>
                    @else
                        <ul class="space-y-2">
                            @foreach($voices as $v)
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
                                                    動画生成
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-6">{{ $voices->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
