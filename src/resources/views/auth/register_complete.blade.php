
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            会員登録完了
        </h2>
    </x-slot>

    <div class="max-w-md mx-auto mt-10 p-6 bg-white rounded shadow">
        <p class="mb-4">ようこそ、{{ $user->name }}さん！<br>初期クレジット付与のため、クーポンコードをお持ちの場合はご入力ください。</p>
        <form method="POST" action="{{ route('register.complete.store') }}">
            @csrf
            <div class="mb-4">
                <label for="coupon_code" class="block text-sm font-medium text-gray-700">クーポンコード（任意）</label>
                <input type="text" name="coupon_code" id="coupon_code" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old('coupon_code') }}">
                @error('coupon_code')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">クレジット付与</button>
        </form>
    </div>
</x-app-layout>
