<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AIあべむつき</title>
  <link rel="icon" type="image/png" href="./favicon.png">
  
  <link rel="stylesheet" href="./css/style.css" data-navigate-track="reload" />
  <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-M7BLLFKV');</script>
<!-- End Google Tag Manager -->
</head>

<body class="bg-gradient-to-br from-blue-50 via-white to-purple-50 min-h-screen">
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-M7BLLFKV"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<header class="bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-50">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
      <div class="flex items-center space-x-2">
        <img src="./images/logo.png" class="h-12 w-auto" alt="AIあべむつきロゴ">

      </div>
      <nav class="hidden md:flex space-x-8">
        <a href="#features" class="text-gray-600 hover:text-blue-600 transition-colors">機能</a>
        <a href="#use-cases" class="text-gray-600 hover:text-blue-600 transition-colors">活用例</a>
        <a href="#pricing" class="text-gray-600 hover:text-blue-600 transition-colors">料金</a>
        <a href="/support" class="text-gray-600 hover:text-blue-600 transition-colors">お問い合わせ</a>
      </nav>

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

<section class="w-screen overflow-hidden">
  <picture>
    <source media="(max-width: 767px)" srcset="images/header_m.jpg">
    <img src="images/header.jpg"
         alt="AIあべむつき ヒーロー画像"
         class="w-full h-[400px] md:h-[600px] object-cover mb-8 rounded-none shadow-lg" />
  </picture>
</section>


<section class="container mx-auto px-4 pb-20 pt-0 text-center">
    
    <div style="height:60px;"></div>
    <span
      class="inline-flex items-center justify-center rounded-md border px-2 py-0.5 text-lg font-medium w-fit whitespace-nowrap mb-6 bg-blue-100 text-blue-800 hover:bg-blue-200">🎉
      新サービス公開！</span>
      <div style="height:30px;"></div>
    <style>
/* グラデーションが流れる */
@keyframes gradientMove {
  0% { background-position: 0% 50%; }
  100% { background-position: 200% 50%; }
}
.animate-gradientMove {
  background-size: 200% auto;
  animation: gradientMove 6s linear infinite;
}

/* フェードイン＋上方向から */
@keyframes fadeInUp {
  0% {
    opacity: 0;
    transform: translateY(30px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}
.animate-fadeInUp {
  animation: fadeInUp 1.2s ease-out forwards;
}

/* 両方同時に適用できるように */
.combined-animation {
  background-size: 200% auto;
  animation:
    gradientMove 6s linear infinite,
    fadeInUp 1.2s ease-out;
}
</style>

<h2
  class="text-3xl sm:text-4xl md:text-6xl lg:text-6xl font-bold mb-5 sm:mb-6 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent leading-relaxed whitespace-normal break-words combined-animation">
  あなたのメッセージを<br>あべむつきが語り演じる
</h2>

    <div style="height:20px;"></div> <p class="text-base sm:text-lg text-gray-600 mb-6 max-w-2xl mx-auto leading-relaxed px-4">
  AIあべむつきで、
  <br class="md:hidden"> 
  動画制作の常識が変わる。
  <br class="md:hidden"> 
  あべむつきのリアルな声とアバターで、
  <br class="md:hidden"> 
  誰でも簡単にプロ品質の動画を生成。
  <br class="md:hidden"> 
  ビジネスからプライベートまで、
  <br class="md:hidden"> 
  あなたのアイデアを形に。
</p>
<div style="height:30px;"></div>

<div class="flex flex-col sm:flex-row gap-4 max-w-xl mx-auto mb-12">
<a href="{{ route('register') }}" class="flex-1 inline-flex items-center justify-center gap-2 rounded-md text-lg font-semibold h-10 px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:from-blue-700 hover:to-purple-700 transition-all">今すぐ無料で始める</a>
<a href="https://www.youtube.com/watch?v=cqjrpFWFtm4"
   class="flex-1 inline-flex items-center justify-center gap-2 rounded-md text-lg font-medium h-10 px-8 py-4 border-2 border-gray-300 text-gray-800 bg-white hover:bg-gray-100 hover:shadow-lg hover:-translate-y-0.5 transition-all">
  デモを見る
</a>
</div>

    <div style="height:100px;"></div>
    <div class="max-w-4xl mx-auto">
      <div
        class="relative bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl overflow-hidden shadow-2xl cursor-pointer group">
        <div class="aspect-video flex items-center justify-center">
          <iframe width="100%" height="100%" src="https://www.youtube.com/embed/hNq2LtdgkYU" title="YouTubeデモ動画"
      frameborder="0"
      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
      allowfullscreen style="border-radius: 12px;"></iframe>
        </div>
      
        </div>
      </div>
    </div>
    <div style="height:100px;"></div>
  </section>

 



  <section id="features" class="py-20 bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50">
    <div class="container mx-auto px-4 relative">
      <div
        class="absolute -top-10 left-1/2 -translate-x-1/2 w-32 h-32 bg-gradient-to-tr from-blue-200 via-purple-200 to-pink-200 rounded-full blur-2xl opacity-60">
      </div>
      <div class="text-center mb-16">
        <h3
          class="text-3xl sm:text-4xl font-extrabold mb-3 sm:mb-4 inline-block text-blue-700 drop-shadow-lg animate-fade-in">
          ＼ 主な機能 ／</h3>
        <p class="text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto animate-fade-in delay-200 px-4">
          最先端のAI技術で、あべむつき氏の魅力を完全再現</p>
      </div>
      <div class="grid md:grid-cols-3 gap-8">

        <!-- リアルな音声合成 -->
        <div
          class="group bg-white/70 backdrop-blur-lg border border-blue-100 shadow-xl rounded-2xl py-8 px-6 flex flex-col gap-6 text-center hover:scale-105 hover:shadow-2xl transition-all duration-300 animate-fade-in-up">
          <!-- 画像 -->
          <img src="./images/ai-avatar-motion-visual03.jpg" alt="リアルな音声合成"
            class="w-full rounded-xl shadow-md mb-6 object-cover">
          <!-- アイコン -->
          <div
            class="mx-auto mb-4 p-4 bg-gradient-to-tr from-blue-100 via-purple-100 to-pink-100 rounded-full w-fit shadow-lg group-hover:rotate-12 transition-transform duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-mic h-10 w-10 text-blue-500" aria-hidden="true">
              <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"></path>
              <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
              <line x1="12" x2="12" y1="19" y2="22"></line>
            </svg>
          </div>
          <div class="font-bold text-xl text-blue-700 drop-shadow-sm">リアルな音声合成</div>
          <div class="px-6">
            <div class="text-gray-600 text-base leading-relaxed text-left">あべむつき氏の自然で聞き取りやすい声で、入力したテキストを感情豊かに読み上げます。
            </div>
          </div>
        </div>

        <!-- 表現豊かなアバター -->
        <div
          class="group bg-white/70 backdrop-blur-lg border border-purple-100 shadow-xl rounded-2xl py-8 px-6 flex flex-col gap-6 text-center hover:scale-105 hover:shadow-2xl transition-all duration-300 animate-fade-in-up delay-100">
          <!-- 画像 -->
          <img src="./images/ai-touch-interaction-visual.jpg" alt="表現豊かなアバター"
            class="w-full rounded-xl shadow-md mb-6 object-cover">
          <!-- アイコン -->
          <div
            class="mx-auto mb-4 p-4 bg-gradient-to-tr from-purple-100 via-pink-100 to-blue-100 rounded-full w-fit shadow-lg group-hover:rotate-12 transition-transform duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-video h-10 w-10 text-purple-500" aria-hidden="true">
              <path d="m16 13 5.223 3.482a.5.5 0 0 0 .777-.416V7.87a.5.5 0 0 0-.752-.432L16 10.5"></path>
              <rect x="2" y="6" width="14" height="12" rx="2"></rect>
            </svg>
          </div>
          <div class="font-bold text-xl text-purple-700 drop-shadow-sm">表現豊かなアバター</div>
          <div class="px-6">
            <div class="text-gray-600 text-base leading-relaxed text-left">音声に合わせて自然な口の動きや表情、ジェスチャーでリアルな動画を生成します。</div>
          </div>
        </div>

        <!-- 直感的な操作 -->
        <div
          class="group bg-white/70 backdrop-blur-lg border border-green-100 shadow-xl rounded-2xl py-8 px-6 flex flex-col gap-6 text-center hover:scale-105 hover:shadow-2xl transition-all duration-300 animate-fade-in-up delay-200">
          <!-- 画像 -->
          <img src="./images/ai-intuitive-operation-visual.jpg" alt="直感的な操作"
            class="w-full rounded-xl shadow-md mb-6 object-cover">
          <!-- アイコン -->
          <div
            class="mx-auto mb-4 p-4 bg-gradient-to-tr from-green-100 via-blue-100 to-purple-100 rounded-full w-fit shadow-lg group-hover:rotate-12 transition-transform duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-sparkles h-10 w-10 text-green-500" aria-hidden="true">
              <path
                d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z">
              </path>
              <path d="M20 3v4"></path>
              <path d="M22 5h-4"></path>
              <path d="M4 17v2"></path>
              <path d="M5 18H3"></path>
            </svg>
          </div>
          <div class="font-bold text-xl text-green-700 drop-shadow-sm">直感的な操作</div>
          <div class="px-6">
            <div class="text-gray-600 text-base leading-relaxed text-left">テキスト入力から動画生成まで、シンプルな操作で完結。専門知識は一切不要です。</div>
          </div>
        </div>
        <div style="height:50px;"></div>
      </div>

      <!-- アニメーション用CSS -->
      <style>
        @keyframes fade-in {
          from {
            opacity: 0;
            transform: translateY(20px);
          }

          to {
            opacity: 1;
            transform: translateY(0);
          }
        }

        @keyframes fade-in-up {
          from {
            opacity: 0;
            transform: translateY(40px);
          }

          to {
            opacity: 1;
            transform: translateY(0);
          }
        }

        .animate-fade-in {
          animation: fade-in 1s cubic-bezier(.4, 0, .2, 1) both;
        }

        .animate-fade-in-up {
          animation: fade-in-up 1s cubic-bezier(.4, 0, .2, 1) both;
        }

        .delay-100 {
          animation-delay: .1s;
        }

        .delay-200 {
          animation-delay: .2s;
        }
      </style>
    </div>
    <style>
      @keyframes fade-in {
        from {
          opacity: 0;
          transform: translateY(20px);
        }

        to {
          opacity: 1;
          transform: translateY(0);
        }
      }

      @keyframes fade-in-up {
        from {
          opacity: 0;
          transform: translateY(40px);
        }

        to {
          opacity: 1;
          transform: translateY(0);
        }
      }

      .animate-fade-in {
        animation: fade-in 1s cubic-bezier(.4, 0, .2, 1) both;
      }

      .animate-fade-in-up {
        animation: fade-in-up 1s cubic-bezier(.4, 0, .2, 1) both;
      }

      /* delay-300 も追加して、3枚目のカードに適用します */
      .delay-100 {
        animation-delay: .1s;
      }

      .delay-200 {
        animation-delay: .2s;
      }

      .delay-300 {
        animation-delay: .3s;
      }
    </style>
</section>



    <section id="use-cases" class="py-20 bg-gray-50">

      <div class="container mx-auto px-4">
        <div style="height:50px;"></div>
        <div class="text-center mb-16">
          <h3 class="text-3xl sm:text-4xl font-bold mb-3 sm:mb-4 animate-fade-in">活用シーン</h3>
          <p class="text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto animate-fade-in delay-100 px-4">
            様々な場面で、
            <br class="md:hidden">
            あなたのメッセージを効果的に伝える
          </p>
          <div class="flex justify-center mt-4 animate-fade-in delay-200">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round" style="opacity:0.7;">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </div>
        </div>
        <div style="height:50px;"></div>

        <div class="grid md:grid-cols-1 lg:grid-cols-3 gap-8">

          <div
            class="bg-white flex flex-col gap-6 rounded-xl hover:shadow-lg transition-shadow border border-gray-100 shadow-md p-6 text-center animate-fade-in-up delay-100">
            <div class="flex flex-col items-center text-center space-y-4 pb-4">
              <img src="./images/youtube.jpg" alt="YouTube" class="w-24 h-24 object-contain" />
              <div class="text-3xl font-bold text-red-600">YouTube</div>
            </div>
            <ul class="space-y-3 text-lg text-gray-700 text-left">
              <li class="flex items-center"><span class="text-green-600 mr-3">✔</span>解説動画</li>
              <li class="flex items-center"><span class="text-green-600 mr-3">✔</span>ショート動画</li>
              <li class="flex items-center"><span class="text-green-600 mr-3">✔</span>商品レビュー動画</li>
              <li class="flex items-center"><span class="text-green-600 mr-3">✔</span>Vlog（ライフスタイル／ビジネス紹介）</li>
              <li class="flex items-center"><span class="text-green-600 mr-3">✔</span>コラボ企画動画</li>
            </ul>

          </div>

          <div
            class="bg-white flex flex-col gap-6 rounded-xl hover:shadow-lg transition-shadow border border-gray-100 shadow-md p-6 text-center animate-fade-in-up delay-200">
            <div class="flex flex-col items-center text-center space-y-4 pb-4">
              <img src="./images/instagram01.jpg" alt="SNS" class="w-24 h-24 object-contain" />
              <div class="text-3xl font-bold text-purple-700">SNS</div>
            </div>
            <ul class="space-y-3 text-lg text-gray-700">
              <li class="flex items-center"><span class="text-green-600 mr-3">✔</span>Instagram（リール・フィード投稿）</li>
              <li class="flex items-center"><span class="text-green-600 mr-3">✔</span>TikTok（ショート動画）</li>
              <li class="flex items-center"><span class="text-green-600 mr-3">✔</span>X（旧Twitter）投稿用動画</li>
              <li class="flex items-center"><span class="text-green-600 mr-3">✔</span>Facebook広告クリエイティブ</li>
              <li class="flex items-center"><span class="text-green-600 mr-3">✔</span>LINE配信用ショートコンテンツ</li>
            </ul>

          </div>

          <div
            class="bg-white flex flex-col gap-6 rounded-xl hover:shadow-lg transition-shadow border border-gray-100 shadow-md p-6 text-center animate-fade-in-up delay-300">
            <div class="flex flex-col items-center text-center space-y-4 pb-4">
              <img src="./images/274710.jpg" alt="教育コンテンツ" class="w-24 h-24 object-contain rounded-lg" />
              <div class="text-3xl font-bold">教育コンテンツ</div>
            </div>
            <ul class="space-y-3 text-lg text-gray-700 text-left">
              <li class="flex items-center"><span class="text-green-600 mr-3">✔</span>講義動画</li>
              <li class="flex items-center"><span class="text-green-600 mr-3">✔</span>カリキュラム教材</li>
              <li class="flex items-center"><span class="text-green-600 mr-3">✔</span>マニュアル解説</li>
              <li class="flex items-center"><span class="text-green-600 mr-3">✔</span>Q&amp;A動画</li>
              <li class="flex items-center"><span class="text-green-600 mr-3">✔</span>受講者向けフォローアップ教材</li>
            </ul>

          </div>

        </div>
      </div>
    </section>

    <section id="pricing" class="py-20 bg-white">
      <div class="container mx-auto px-4">
        <div class="text-center mb-20 relative">
          <div
            class="inline-block bg-gradient-to-r from-yellow-400 via-amber-500 to-yellow-600 text-gray-900 drop-shadow-sm text-sm font-semibold tracking-wider px-5 py-2 rounded-full shadow-md mb-6">
            PRICE PLAN
          </div>
          <h3 class="text-4xl md:text-5xl font-extrabold mb-4 text-gray-900 drop-shadow-sm">
            料金プラン
          </h3>
          <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto leading-relaxed">
            あなたのニーズに合わせて選べる、
            <br class="md:hidden">
            シンプルで分かりやすい料金体系です。
          </p>

          <!-- 飾りライン -->
          <div class="w-24 h-1 bg-gradient-to-r from-amber-400 to-yellow-500 mx-auto mt-6 rounded-full"></div>

        </div>
<!-- 
        <div
          class="bg-gray-50 py-12 px-4 sm:px-8 md:py-16 md:px-12 rounded-lg shadow-md mb-8 sm:mb-12 max-w-5xl mx-auto text-center">
          <h4 class="text-2xl sm:text-3xl font-bold text-blue-700 mb-3 sm:mb-4">【まずは無料お試し】</h4>
          <p class="text-base sm:text-lg text-gray-700 mb-5 sm:mb-6 px-2">最大5分のあべむつき動画が無料で作成できます</p>
          <button
            class="inline-flex items-center justify-center gap-2 whitespace-nowrap font-medium transition-all disabled:pointer-events-none disabled:opacity-50 shadow-xs h-10 rounded-md bg-blue-600 hover:bg-blue-700 text-base sm:text-lg px-6 py-3 text-white">無料でAIあべむつきを始める</button>
        </div> -->
        <div style="height:50px;"></div>

        <div class="bg-gray-50 py-16 px-8 md:py-20 md:px-12 rounded-lg shadow-md mb-12 max-w-5xl mx-auto text-center">
          <div class="px-4 md:px-0">
            <h4 class="font-bold text-gray-800 mb-2 md:mb-4 leading-tight break-words
             text-2xl sm:text-3xl md:text-4xl">
              【安心のプリペイド式（5,000円〜）】
            </h4>

            <p class="text-gray-700 leading-relaxed break-words
            text-base sm:text-lg md:text-xl mb-6 md:mb-12">
              使う分だけクレジットを購入可能。
              <br class="md:hidden">
              料金は以下のとおり：
            </p>
          </div>

          <style>
            /* スマホで縦積みされるテーマ対策（display:block系の上書きを無効化） */
            @media (max-width: 767px) {
              .pricing-table table {
                display: table;
                width: 100%;
                border-collapse: collapse;
              }

              .pricing-table thead {
                display: table-header-group;
              }

              .pricing-table tbody {
                display: table-row-group;
              }

              .pricing-table tr {
                display: table-row;
              }

              .pricing-table th,
              .pricing-table td {
                display: table-cell;
              }
            }
          </style>

          <div class="pricing-table overflow-x-auto">
            <table class="w-full min-w-[720px] md:min-w-0 text-left border-collapse text-gray-800 text-lg">
              <thead>
                <tr class="bg-gradient-to-r from-blue-600 to-purple-600 text-white">
                  <th class="py-4 px-6 rounded-tl-lg whitespace-nowrap">料金</th>
                  <th class="py-4 px-6 whitespace-nowrap">クレジット数</th>
                  <th class="py-4 px-6 rounded-tr-lg whitespace-nowrap">利用目安（動画生成時間）</th>
                </tr>
              </thead>
              <tbody class="bg-white">
                <tr class="border-b hover:bg-gray-50 transition">
                  <td class="py-4 px-6 font-semibold whitespace-nowrap">5,000円</td>
                  <td class="py-4 px-6 whitespace-nowrap">40クレジット</td>
                  <td class="py-4 px-6 whitespace-nowrap">約20分の動画生成が可能</td>
                </tr>
                <tr class="border-b hover:bg-gray-50 transition">
                  <td class="py-4 px-6 font-semibold whitespace-nowrap">10,000円</td>
                  <td class="py-4 px-6 whitespace-nowrap">100クレジット</td>
                  <td class="py-4 px-6 whitespace-nowrap">約50分の動画生成が可能</td>
                </tr>
                <tr class="hover:bg-gray-50 transition">
                  <td class="py-4 px-6 font-semibold whitespace-nowrap">30,000円</td>
                  <td class="py-4 px-6 whitespace-nowrap">400クレジット</td>
                  <td class="py-4 px-6 whitespace-nowrap">約200分の動画生成が可能</td>
                </tr>
              </tbody>
            </table>
          </div>

        </div>


        <div style="height:50px;"></div>
        <div
          class="bg-purple-50 py-20 px-10 md:py-24 md:px-14 rounded-2xl shadow-lg max-w-5xl mx-auto text-center mt-24 mb-24 relative">
          <div class="px-4 md:px-0 text-center">
            <h4 class="font-bold text-purple-700 leading-tight text-2xl sm:text-3xl md:text-3xl mb-3 md:mb-4">
              あべラボ参加でさらにお得
            </h4>

            <p class="text-gray-700 leading-snug md:leading-relaxed text-base sm:text-lg md:text-lg mb-6">
              今なら期間限定で、
              <br class="mobile-break"> オンラインサロン<br class="md:hidden">「あべラボ」に参加すると
              <br class="mobile-break"> <span class="block md:inline">毎月利用可能なクレジットを</span>
              <span class="font-semibold text-purple-700">10分分（20クレジット）プレゼント！</span>
            </p>
            
          </div>
                  <a href="https://abe-labo.biz/pages/landing/campaign.php" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-2 whitespace-nowrap font-medium transition-all disabled:pointer-events-none disabled:opacity-50 shadow-xs h-10 rounded-md bg-purple-600 hover:bg-purple-700 text-lg px-8 py-4 mb-4 text-white">AIあべむつき特典付きあべラボの参加はこちらから</a>
          <p class="text-sm text-gray-600 mb-4">※ クレジット特典は<br class="md:hidden">上記ページからのお申し込み限定です。</p>
          <!-- <p class="text-lg text-gray-700">
            <strong class="text-purple-600 font-bold text-5xl my-6 block">さらに！</strong>
            <br>
            あべラボ入会特典として、
            <br class="md:hidden">
            <span class="font-semibold text-xl text-gray-800">限定のあべむつきアバターも<br class="md:hidden">利用可能。</span>
          </p> -->
          <img src="images/abe_photo.png" alt="あべむつき" class="abe-photo-animation">
        </div>

      </div>
      <div style="height:50px;"></div>
    </section>
    
    <section class="py-20 bg-gradient-to-r from-blue-600 to-purple-600 text-white">
      <div class="container mx-auto px-4">
        <div class="text-center mb-16">
          <h3 class="text-4xl font-bold mb-4">AIあべむつきを<span class="block md:inline"></span>選ぶ理由</h3>
          <p class="text-xl opacity-90 max-w-2xl mx-auto">従来の動画制作の常識を覆す、革新的なメリット</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-8">
          <div class="text-center">
            <img src="images/merit_01.png" alt="時間とコストの大幅削減"
              class="mx-auto mb-4 w-full h-auto max-w-[150px] md:max-w-[200px] lg:max-w-[250px]">
            <h4 class="text-lg font-semibold">撮影・編集の工程をAIが代行。<br>制作期間と外注費を大幅にカットします。</h4>
          </div>
          <div class="text-center">
            <img src="images/merit_02.png" alt="一貫したブランドイメージ"
              class="mx-auto mb-4 w-full h-auto max-w-[150px] md:max-w-[200px] lg:max-w-[250px]">
            <h4 class="text-lg font-semibold">アバターが24時間稼働。<br>常に安定したブランドの顔。</h4>
          </div>
          <div class="text-center">
            <img src="images/merit_03.png" alt="表現の幅が広がる"
              class="mx-auto mb-4 w-full h-auto max-w-[150px] md:max-w-[200px] lg:max-w-[250px]">
            <h4 class="text-lg font-semibold">ロケも移動も不要。<br>想像力通りの映像を簡単に実現。</h4>
          </div>
          <div class="text-center">
            <img src="images/merit_04.png" alt="誰でもクリエイターに"
              class="mx-auto mb-4 w-full h-auto max-w-[150px] md:max-w-[200px] lg:max-w-[250px]">
            <h4 class="text-lg font-semibold">複雑なスキル不要。<br>テキスト入力だけで即座に動画化。</h4>
          </div>
        </div>
      </div>
    </section>


    <section class="py-20 bg-gradient-to-r from-blue-600 to-purple-600 text-white">
      <div class="container mx-auto px-4 text-center">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-white py-12 sm:py-16 lg:py-24">
          <div class="text-center">
            <h2 class="text-2xl sm:text-3xl md:text-3xl lg:text-6xl font-extrabold leading-tight mb-4 sm:mb-6">
              あなたの毎日を革新する<br class="sm:hidden">
              AIあべむつきを<br class="md:hidden">今すぐ体験！</span>
            </h2>
            <p class="text-base sm:text-lg md:text-xl opacity-90 mb-8 sm:mb-10 max-w-2xl mx-auto px-2">
              無料で始められるAIパートナー。<br>
              新しい発見と効率性を、あなたの手に。
            </p>
            <a href="{{ route('register') }}" class="inline-block bg-white text-blue-600 font-bold text-lg sm:text-xl px-8 sm:px-10 py-3 sm:py-4 rounded-full shadow-5lg 
hover:bg-gray-100 transition duration-300 transform hover:scale-105 hover:shadow-2xl animate-float">
              まずは無料登録
              <svg class="w-5 h-5 ml-2 inline-block -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3">
                </path>
              </svg>
            </a>

            <style>
              @keyframes float {

                0%,
                100% {
                  transform: translateY(0);
                }

                50% {
                  transform: translateY(-4px);
                }
              }

              .animate-float {
                animation: float 3s ease-in-out infinite;
              }
            </style>

          </div>
        </div>
      </div>
    </section>

    
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
            <li><a href="/support" class="hover:text-white transition-colors">サポートセンター</a></li>
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