<x-app-layout>
    <div class="max-w-lg mx-auto mt-10 p-6 bg-white rounded shadow">
        <h2 class="text-xl font-bold mb-4">クレジット付与確認</h2>
        @if ($alreadyCharged)
            <div class="mb-4 text-green-600 font-semibold">この購入IDはすでにクレジット付与済みです。</div>
            <a href="{{ route('dashboard') }}" class="btn btn-primary">ダッシュボードへ</a>
        @else
            <div class="mb-4">
                <div><span class="font-semibold">ユーザー名:</span> {{ $user->name }}</div>
                <div><span class="font-semibold">メールアドレス:</span> {{ $user->email }}</div>
                <div><span class="font-semibold">付与クレジット:</span> {{ $pending->credit }} クレジット</div>
            </div>
            <form method="POST" action="{{ route('charge.store') }}" class="flex flex-col gap-4 mt-6">
                @csrf
                <input type="hidden" name="purchase_uid" value="{{ $purchaseUid }}">
                <input type="hidden" name="email" value="{{ $email }}">
                <button type="submit"
  class="w-full py-4 px-6 rounded-xl bg-blue-600 text-white font-bold text-xl shadow-md
         hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2
         transition flex items-center justify-center gap-3">
  <span class="text-2xl">💰</span>
  <span>クレジットを付与する（確定）</span>
</button>

<a href="{{ route('dashboard') }}"
   class="w-full py-3 px-4 rounded-lg bg-white text-gray-700 font-medium text-base border border-gray-300
          hover:bg-gray-50 hover:text-gray-800 transition flex items-center justify-center gap-2">
  <span class="text-2xl">🏠</span>
  <span>ダッシュボードへ戻る</span>
</a>

            </form>
        @endif
    </div>
</x-app-layout>
