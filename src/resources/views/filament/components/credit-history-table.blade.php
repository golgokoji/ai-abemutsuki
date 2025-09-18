<table class="table-auto w-full text-sm">
    <thead>
        <tr class="bg-gray-100">
            <th class="px-2 py-1">日時</th>
            <th class="px-2 py-1">クレジット数</th>
            <th class="px-2 py-1">金額</th>
            <th class="px-2 py-1">備考</th>
            <th class="px-2 py-1">付与者</th>
            <th class="px-2 py-1">注文ID</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($histories as $history)
            <tr class="border-b">
                <td class="px-2 py-1">{{ $history->granted_at ?? $history->created_at }}</td>
                <td class="px-2 py-1 text-right">{{ $history->credit }}</td>
                <td class="px-2 py-1 text-right">{{ $history->amount }}</td>
                <td class="px-2 py-1">{{ $history->note }}</td>
                <td class="px-2 py-1">{{ $history->system }}</td>
                <td class="px-2 py-1">{{ $history->order_id }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-2 py-2 text-center text-gray-400">履歴はありません</td></tr>
        @endforelse
    </tbody>
</table>
