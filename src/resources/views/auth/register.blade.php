
<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-amber-50 via-white to-indigo-50 px-4 py-12">
        <div class="w-full max-w-md">
            <div class="bg-white/80 backdrop-blur-sm shadow-xl rounded-2xl p-8 border border-gray-100">
                <!-- Brand / Title -->
                <div class="text-center">
                    <div class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-amber-100 mb-3">
                        <svg viewBox="0 0 24 24" class="h-6 w-6 text-amber-600" fill="currentColor" aria-hidden="true">
                            <path d="M12 2a10 10 0 1 0 10 10h-2a8 8 0 1 1-8-8V2z"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900">AIあべむつき 会員登録</h1>
                    <p class="mt-1 text-sm text-gray-500">パスワード不要、Google でかんたん登録</p>
                </div>

                <!-- CTA -->
                <div class="mt-6">
                    <a href="{{ route('google.login') }}"
                       class="group relative w-full inline-flex items-center justify-center gap-3 rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-4 focus:ring-amber-200 transition">
                        <!-- Google G -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="h-5 w-5" aria-hidden="true">
                            <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3c-1.6 4.6-6 8-11.3 8-6.6 0-12-5.4-12-12S17.4 12 24 12c3 0 5.7 1.1 7.8 3L37 9.8C33.7 6.8 29.1 5 24 5 12.9 5 4 13.9 4 25s8.9 20 20 20 20-8.9 20-20c0-1.6-.2-3.1-.4-4.5z"/>
                            <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.5 15.2 18.9 12 24 12c3 0 5.7 1.1 7.8 3L37 9.8C33.7 6.8 29.1 5 24 5 16.2 5 9.6 9.4 6.3 14.7z"/>
                            <path fill="#4CAF50" d="M24 45c5 0 9.5-1.9 12.9-5.1l-6-4.9C29 36.7 26.6 37.6 24 37.6c-5.3 0-9.8-3.4-11.3-8l-6.6 5.1C9.4 40.7 16.1 45 24 45z"/>
                            <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-0.8 2.3-2.4 4.3-4.5 5.6l6 4.9C39.8 35.9 44 30.9 44 25c0-1.6-.2-3.1-.4-4.5z"/>
                        </svg>
                        <span>Googleアカウントで会員登録</span>
                    </a>
                </div>

                <!-- Benefits -->
                <ul class="mt-6 space-y-2 text-sm text-gray-600">
                    <li class="flex items-start gap-2">
                        <svg class="h-5 w-5 mt-0.5 text-emerald-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0L3.293 9.207a1 1 0 111.414-1.414l3.043 3.043 6.543-6.543a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Googleアカウントで数秒で登録完了
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="h-5 w-5 mt-0.5 text-emerald-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0L3.293 9.207a1 1 0 111.414-1.414l3.043 3.043 6.543-6.543a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        安心のプリペイド形式なので、自動的に課金されることもありません。
                    </li>
                </ul>

                <!-- Divider -->
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                        <div class="relative flex justify-center">
                            <span class="bg-white px-3 text-xs text-gray-400">ご利用にあたって</span>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="mt-3 space-y-2 text-xs leading-5 text-gray-600">
                    <p>AIあべむつきのご利用には <strong>Googleアカウント</strong> が必要になります。</p>
                    <p>
                        Googleアカウントをお持ちでない方は
                        <a href="https://accounts.google.com/signup?hl=ja" target="_blank" rel="noopener" class="underline text-indigo-600 hover:text-indigo-700">
                            こちら（Google公式：アカウント作成）
                        </a>
                        から作成してください。
                    </p>
                    <p class="text-gray-500">
                        「Googleアカウントで会員登録」をクリックすると、利用規約およびプライバシーポリシーに同意したものとみなされます。
                    </p>
                </div>

                <!-- Links -->
                <div class="mt-4 text-xs space-y-2">
                    <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-700 underline underline-offset-2 block">
                        すでに登録済みの方はこちら
                    </a>
                    <div class="space-x-3">
                        <a href="{{ url('/terms') }}" class="text-gray-400 hover:text-gray-600 underline underline-offset-2">利用規約</a>
                        <a href="{{ url('/privacy') }}" class="text-gray-400 hover:text-gray-600 underline underline-offset-2">プライバシーポリシー</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
