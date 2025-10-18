<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    
        <body class="bg-gradient-to-br from-blue-50 via-white to-purple-50 min-h-screen">
            <header class="bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-50">
                <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                    <div class="flex items-center space-x-2">
                        <a href="/"><img src="/logo.png" class="h-12 w-auto" alt="AIあべむつきロゴ"></a>
                    </div>


      @if (Auth::check())
      <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-md text-sm font-medium h-9 px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:from-blue-700 hover:to-purple-700">ダッシュボード</a>
      @else

      <div class="flex items-center space-x-2 ml-auto">
        <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 rounded-md text-sm font-medium h-9 px-4 py-2 border border-blue-600 text-blue-600 hover:bg-blue-50 mr-2">ログイン</a>
        <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 rounded-md text-sm font-medium h-9 px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:from-blue-700 hover:to-purple-700">会員登録</a>
      </div>
      @endif
                </div>
            </header>
            <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
                <div class="w-full sm:max-w-3xl mt-6 px-8 py-6 bg-white shadow-md overflow-hidden sm:rounded-2xl">
                    {{ $slot }}
                </div>
            </div>


    <footer id="contact" class="bg-gray-900 text-white py-16">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles h-5 w-5 text-white" aria-hidden="true">
                                <path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"></path>
                                <path d="M20 3v4"></path>
                                <path d="M22 5h-4"></path>
                                <path d="M4 17v2"></path>
                                <path d="M5 18H3"></path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold">AIあべむつき</h4>
                    </div>
                    <p class="text-gray-400">あなたのメッセージを、あべむつきが語り、演じる革新的なAIサービス</p>
                </div>
                <div>
                    <h5 class="font-semibold mb-4">サービス</h5>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#features" class="hover:text-white transition-colors">機能紹介</a></li>
                        <li><a href="#pricing" class="hover:text-white transition-colors">料金プラン</a></li>

                    </ul>
                </div>
                <div>
                    <h5 class="font-semibold mb-4">サポート</h5>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">サポートセンター</a></li>
                        <li><a href="/faq" class="hover:text-white transition-colors">よくある質問</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-semibold mb-4">法的情報</h5>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/terms" class="hover:text-white transition-colors">利用規約</a></li>
                        <li><a href="/privacy" class="hover:text-white transition-colors">プライバシーポリシー</a></li>
                        <li><a href="/law" class="hover:text-white transition-colors">特定商取引法</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
                <p>© 2025 AIあべむつき. All rights reserved.</p>
            </div>
        </div>
    </footer>
    </body>


</html>
