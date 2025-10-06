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
    <body class="font-sans text-gray-900 antialiased">
        <body class="bg-gradient-to-br from-blue-50 via-white to-purple-50 min-h-screen">
            <header class="bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-50">
                <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                    <div class="flex items-center space-x-2">
                        <img src="/logo.png" class="h-12 w-auto" alt="AIあべむつきロゴ">
                    </div>
                    <nav class="hidden md:flex space-x-8">
                        <a href="#features" class="text-gray-600 hover:text-blue-600 transition-colors">機能</a>
                        <a href="#use-cases" class="text-gray-600 hover:text-blue-600 transition-colors">活用例</a>
                        <a href="#pricing" class="text-gray-600 hover:text-blue-600 transition-colors">料金</a>
                        <a href="#contact" class="text-gray-600 hover:text-blue-600 transition-colors">お問い合わせ</a>
                    </nav>
                    @if (Auth::check())
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-md text-sm font-medium h-9 px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:from-blue-700 hover:to-purple-700">ダッシュボード</a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 rounded-md text-sm font-medium h-9 px-4 py-2 border border-blue-600 text-blue-600 hover:bg-blue-50 mr-2">ログイン</a>
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 rounded-md text-sm font-medium h-9 px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:from-blue-700 hover:to-purple-700">会員登録</a>
                    @endif
                </div>
            </header>
            <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
                <div class="w-full sm:max-w-3xl mt-6 px-8 py-6 bg-white shadow-md overflow-hidden sm:rounded-2xl">
                    {{ $slot }}
                </div>
            </div>
        </body>
    </body>
</html>
