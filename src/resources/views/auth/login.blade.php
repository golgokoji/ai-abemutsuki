<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- メールアドレス -->
        <div>
            <x-input-label for="email" :value="'メールアドレス'" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- パスワード -->
        <div class="mt-4">
            <x-input-label for="password" :value="'パスワード'" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- ログイン情報を記憶する -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">ログイン情報を記憶する</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    パスワードをお忘れの方はこちら
                </a>
            @endif

            <x-primary-button class="ms-3">
                ログイン
            </x-primary-button>
        </div>
    </form>

<div class="mt-4 flex justify-center">
    <a href="{{ route('google.login') }}"
       class="inline-flex items-center justify-center w-full sm:w-auto px-4 py-2 
              bg-white border border-gray-300 rounded-md shadow-sm
              font-medium text-gray-700 hover:bg-gray-50 
              focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400">
        <!-- Google G ロゴ -->
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="w-5 h-5 mr-2">
            <path fill="#4285F4" d="M24 9.5c3.7 0 6.2 1.6 7.6 2.9l5.6-5.6C33.8 3.5 29.3 1.5 24 1.5 14.8 1.5 7.2 7.4 4.5 15.5l6.9 5.4C12.8 13.5 17.9 9.5 24 9.5z"/>
            <path fill="#34A853" d="M46.1 24.5c0-1.6-.1-2.8-.4-4H24v8.1h12.5c-.5 2.6-2.1 4.7-4.4 6.1l6.8 5.3c3.9-3.6 6.2-8.9 6.2-15.5z"/>
            <path fill="#FBBC05" d="M11.4 28.4c-.5-1.6-.8-3.3-.8-5s.3-3.4.8-5L4.5 12.9C3.2 15.6 2.5 19 2.5 23s.7 7.4 2 10.1l6.9-5.4z"/>
            <path fill="#EA4335" d="M24 45c5.3 0 9.8-1.7 13.1-4.7l-6.8-5.3c-1.9 1.3-4.3 2.1-6.3 2.1-6.1 0-11.2-4-12.9-9.4l-6.9 5.4C7.2 40.6 14.8 45 24 45z"/>
        </svg>
        <span>Googleでログイン</span>
    </a>
</div>


{{-- resources/views/auth/login.blade.php --}}

</x-guest-layout>
