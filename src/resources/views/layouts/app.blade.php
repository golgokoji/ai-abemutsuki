<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/png" href="/favicon.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                @if(session('error'))
                    <div class="max-w-2xl mx-auto my-4">
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative flex items-center shadow">
                            <svg class="w-6 h-6 mr-2 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-semibold">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif
                @if(session('status'))
                    <div class="max-w-2xl mx-auto my-4">
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative flex items-center shadow">
                            <svg class="w-6 h-6 mr-2 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="font-semibold">{{ session('status') }}</span>
                        </div>
                    </div>
                @endif

                {{ $slot }}
            </main>
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
            <li><a href="#" class="hover:text-white transition-colors">よくある質問</a></li>
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
