<x-app-layout>
    <div class="max-w-md mx-auto mt-10 p-6 bg-white rounded shadow">
        <h2 class="text-xl font-bold mb-4">クーポンコード入力</h2>
        <form method="POST" action="{{ route('coupon.store') }}" class="flex flex-col gap-4">
            @csrf
            <label for="coupon_code" class="font-semibold">クーポンコード</label>
            <input type="text" name="coupon_code" id="coupon_code" class="border rounded px-3 py-2" required maxlength="32">
            <button type="submit" class="py-3 px-4 rounded bg-indigo-600 text-white font-bold text-lg shadow hover:bg-indigo-700 transition">クーポンを利用する</button>
        </form>
    </div>
</x-app-layout>
