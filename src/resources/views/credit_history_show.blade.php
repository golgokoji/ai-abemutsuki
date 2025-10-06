<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            クレジット履歴詳細
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <dl class="divide-y divide-gray-200">
                        <div class="py-2 flex justify-between">
                            <dt class="font-medium text-gray-600">日時</dt>
                            <dd>{{ $history->granted_at ? date('Y/m/d H:i:s', strtotime($history->granted_at)) : '' }}</dd>
                        </div>
                        <div class="py-2 flex justify-between">
                            <dt class="font-medium text-gray-600">クレジット数</dt>
                            <dd class="font-bold">
                                @if($history->credit > 0)+@endif{{ $history->credit }}
                            </dd>
                        </div>
                        <div class="py-2 flex justify-between">
                            <dt class="font-medium text-gray-600">金額</dt>
                            <dd>
                                @if(in_array($history->system, ['coupon', 'payz', 'infotop']))
                                    -
                                @else
                                    {{ $history->amount }}
                                @endif
                            </dd>
                        </div>
                        <div class="py-2 flex justify-between">
                            <dt class="font-medium text-gray-600">種別</dt>
                            <dd>
                                @if($history->system === 'coupon')
                                    クーポンボーナス
                                @elseif($history->system === 'AvatarVideo')
                                    動画作成
                                @else
                                    {{ $history->system }}
                                @endif
                            </dd>
                        </div>

                        <div class="py-2 flex justify-between">
                            <dt class="font-medium text-gray-600">備考</dt>
                            <dd class="max-w-xs break-words">{{ $history->note }}</dd>
                        </div>
                        <div class="py-2 flex justify-between">
                            <dt class="font-medium text-gray-600">注文ID</dt>
                            <dd>{{ $history->order_id }}</dd>
                        </div>
                    </dl>
                    <div class="mt-6">
                        <a href="{{ route('credit_history') }}" class="text-blue-600 hover:underline">一覧に戻る</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
