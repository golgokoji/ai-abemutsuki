
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            音声一覧
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($voices->isEmpty())
                        <p class="text-gray-500">まだ音声がありません。</p>
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
