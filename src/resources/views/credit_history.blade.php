<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            クレジット履歴
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="table-auto w-full text-sm mb-8">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-2 py-1">日時</th>
                                <th class="px-2 py-1">クレジット数</th>
                                <th class="px-2 py-1">種別</th>
                                <th class="px-2 py-1">詳細</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($histories as $history)
                                <tr class="border-b">
                                    <td class="px-2 py-1 whitespace-nowrap">{{ $history->granted_at ? date('Y/m/d H:i:s', strtotime($history->granted_at)) : '' }}</td>
                                    <td class="px-2 py-1 text-right font-bold">
                                        @if($history->credit > 0)+@endif{{ $history->credit }}
                                    </td>
                                    <td class="px-2 py-1">
                                        @if($history->system === 'coupon')
                                            クーポンボーナス
                                        @elseif($history->system === 'AvatarVideo')
                                            動画作成
                                        @else
                                            {{ $history->system }}
                                        @endif
                                    </td>
                                    <td class="px-2 py-1">
                                        <a href="{{ route('credit_history.show', $history->id) }}" class="text-indigo-600 underline">詳細</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-2 py-2 text-center text-gray-400">履歴はありません</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-6">
                        {{ $histories->links() }}
                    </div>
                    <a href="{{ route('dashboard') }}" class="text-blue-600 hover:underline">ダッシュボードに戻る</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
