<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AIあべむつき</title>
  <link rel="icon" type="image/png" href="/favicon.png">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

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
  <section class="container mx-auto px-4 py-20 text-center">
    <span class="inline-flex items-center justify-center rounded-md border px-2 py-0.5 text-xs font-medium w-fit whitespace-nowrap mb-6 bg-blue-100 text-blue-800 hover:bg-blue-200">🎉 新サービス公開！</span>
    <h2 class="text-5xl md:text-6xl font-bold mb-6 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent leading-tight">あなたのメッセージを、<br>あべむつきが語り、演じる</h2>
    <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto leading-relaxed">AIあべむつきで、動画制作の常識が変わる。あべむつきのリアルな声とアバターで、誰でも簡単にプロ品質の動画を生成。ビジネスからプライベートまで、あなたのアイデアを形に。</p>
    <div class="flex flex-col sm:flex-row gap-4 justify-center mb-12">
      <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 rounded-md text-lg font-medium h-10 px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:from-blue-700 hover:to-purple-700">今すぐ無料で始める</a>
      <!-- <a href="#demo" class="inline-flex items-center justify-center gap-2 rounded-md text-lg font-medium h-10 px-8 py-4 border-2 hover:bg-gray-50">デモを見る</a> -->
    </div>
    <div class="max-w-4xl mx-auto">
      <div class="relative bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl overflow-hidden shadow-2xl cursor-pointer group">
        <div class="aspect-video flex items-center justify-center">
          <iframe width="100%" height="100%" src="https://www.youtube.com/embed/_pXfFY4Sgo0?si=17O96i9D7wQ7Ibpy" title="YouTubeデモ動画" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen style="border-radius: 12px;"></iframe>
        </div>
      </div>
    </div>
  </section>
  <section id="features" class="py-20 bg-white">
    <div class="container mx-auto px-4">
      <div class="text-center mb-16">
        <h3 class="text-4xl font-bold mb-4">主な機能</h3>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">最先端のAI技術で、あべむつき氏の魅力を完全再現</p>
      </div>
      <div class="grid md:grid-cols-3 gap-8">
        <div class="bg-card text-card-foreground flex flex-col gap-6 rounded-xl py-6 text-center hover:shadow-lg transition-shadow border-0 shadow-md">
          <div class="mx-auto mb-4 p-3 bg-gray-50 rounded-full w-fit">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mic h-8 w-8 text-blue-600" aria-hidden="true">
              <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"></path>
              <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
              <line x1="12" x2="12" y1="19" y2="22"></line>
            </svg>
          </div>
          <div class="font-semibold text-xl">リアルな音声合成</div>
          <div class="px-6">
            <div class="text-muted-foreground text-base leading-relaxed">あべむつき氏の自然で聞き取りやすい声で、入力したテキストを感情豊かに読み上げます。</div>
          </div>
        </div>
        <div class="bg-card text-card-foreground flex flex-col gap-6 rounded-xl py-6 text-center hover:shadow-lg transition-shadow border-0 shadow-md">
          <div class="mx-auto mb-4 p-3 bg-gray-50 rounded-full w-fit">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-video h-8 w-8 text-purple-600" aria-hidden="true">
              <path d="m16 13 5.223 3.482a.5.5 0 0 0 .777-.416V7.87a.5.5 0 0 0-.752-.432L16 10.5"></path>
              <rect x="2" y="6" width="14" height="12" rx="2"></rect>
            </svg>
          </div>
          <div class="font-semibold text-xl">表現豊かなアバター</div>
          <div class="px-6">
            <div class="text-muted-foreground text-base leading-relaxed">音声に合わせて自然な口の動きや表情、ジェスチャーでリアルな動画を生成します。</div>
          </div>
        </div>
        <div class="bg-card text-card-foreground flex flex-col gap-6 rounded-xl py-6 text-center hover:shadow-lg transition-shadow border-0 shadow-md">
          <div class="mx-auto mb-4 p-3 bg-gray-50 rounded-full w-fit">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles h-8 w-8 text-green-600" aria-hidden="true">
              <path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"></path>
              <path d="M20 3v4"></path>
              <path d="M22 5h-4"></path>
              <path d="M4 17v2"></path>
              <path d="M5 18H3"></path>
            </svg>
          </div>
          <div class="font-semibold text-xl">直感的な操作</div>
          <div class="px-6">
            <div class="text-muted-foreground text-base leading-relaxed">テキスト入力から動画生成まで、シンプルな操作で完結。専門知識は一切不要です。</div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section id="use-cases" class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
      <div class="text-center mb-16">
        <h3 class="text-4xl font-bold mb-4">活用シーン</h3>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">様々な場面で、あなたのメッセージを効果的に伝える</p>
      </div>
      <div class="grid md:grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="bg-card text-card-foreground flex flex-col gap-6 rounded-xl hover:shadow-lg transition-shadow border-0 shadow-md p-6">
          <div class="flex flex-row items-center space-x-4 pb-4">

            <div class="text-3xl font-bold">YouTube</div>
          </div>
          <div class="px-6 pt-4">
            <ul class="space-y-3 text-lg text-gray-700">
              <li class="flex items-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check h-5 w-5 text-green-600 mr-3 flex-shrink-0" aria-hidden="true">
                  <path d="M20 6 9 17l-5-5"></path>
                </svg><span>解説動画</span></li>
              <li class="flex items-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check h-5 w-5 text-green-600 mr-3 flex-shrink-0" aria-hidden="true">
                  <path d="M20 6 9 17l-5-5"></path>
                </svg><span>ショート動画</span></li>
              <li class="flex items-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check h-5 w-5 text-green-600 mr-3 flex-shrink-0" aria-hidden="true">
                  <path d="M20 6 9 17l-5-5"></path>
                </svg><span>商品レビュー動画</span></li>
              <li class="flex items-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check h-5 w-5 text-green-600 mr-3 flex-shrink-0" aria-hidden="true">
                  <path d="M20 6 9 17l-5-5"></path>
                </svg><span>Vlog（ライフスタイル／ビジネス紹介）</span></li>
              <li class="flex items-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check h-5 w-5 text-green-600 mr-3 flex-shrink-0" aria-hidden="true">
                  <path d="M20 6 9 17l-5-5"></path>
                </svg><span>コラボ企画動画</span></li>
            </ul>
          </div>
        </div>
        <div class="bg-card text-card-foreground flex flex-col gap-6 rounded-xl hover:shadow-lg transition-shadow border-0 shadow-md p-6">
          <div class="flex flex-row items-center space-x-4 pb-4">

            <div class="text-3xl font-bold">SNS</div>
          </div>
          <div class="px-6 pt-4">
            <ul class="space-y-3 text-lg text-gray-700">
              <li class="flex items-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check h-5 w-5 text-green-600 mr-3 flex-shrink-0" aria-hidden="true">
                  <path d="M20 6 9 17l-5-5"></path>
                </svg><span>Instagram（リール・フィード投稿）</span></li>
              <li class="flex items-center"><svg xmlns="http://www.w3.org/200/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check h-5 w-5 text-green-600 mr-3 flex-shrink-0" aria-hidden="true">
                  <path d="M20 6 9 17l-5-5"></path>
                </svg><span>TikTok（ショート動画）</span></li>
              <li class="flex items-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check h-5 w-5 text-green-600 mr-3 flex-shrink-0" aria-hidden="true">
                  <path d="M20 6 9 17l-5-5"></path>
                </svg><span>X（旧Twitter）投稿用動画</span></li>
              <li class="flex items-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check h-5 w-5 text-green-600 mr-3 flex-shrink-0" aria-hidden="true">
                  <path d="M20 6 9 17l-5-5"></path>
                </svg><span>Facebook広告クリエイティブ</span></li>
              <li class="flex items-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check h-5 w-5 text-green-600 mr-3 flex-shrink-0" aria-hidden="true">
                  <path d="M20 6 9 17l-5-5"></path>
                </svg><span>LINE配信用ショートコンテンツ</span></li>
            </ul>
          </div>
        </div>
        <div class="bg-card text-card-foreground flex flex-col gap-6 rounded-xl hover:shadow-lg transition-shadow border-0 shadow-md p-6">
          <div class="flex flex-row items-center space-x-4 pb-4">

            <div class="text-3xl font-bold">教育コンテンツ</div>
          </div>
          <div class="px-6 pt-4">
            <ul class="space-y-3 text-lg text-gray-700">
              <li class="flex items-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check h-5 w-5 text-green-600 mr-3 flex-shrink-0" aria-hidden="true">
                  <path d="M20 6 9 17l-5-5"></path>
                </svg><span>講義動画</span></li>
              <li class="flex items-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check h-5 w-5 text-green-600 mr-3 flex-shrink-0" aria-hidden="true">
                  <path d="M20 6 9 17l-5-5"></path>
                </svg><span>カリキュラム教材</span></li>
              <li class="flex items-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check h-5 w-5 text-green-600 mr-3 flex-shrink-0" aria-hidden="true">
                  <path d="M20 6 9 17l-5-5"></path>
                </svg><span>マニュアル解説</span></li>
              <li class="flex items-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check h-5 w-5 text-green-600 mr-3 flex-shrink-0" aria-hidden="true">
                  <path d="M20 6 9 17l-5-5"></path>
                </svg><span>Q&amp;A動画</span></li>
              <li class="flex items-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check h-5 w-5 text-green-600 mr-3 flex-shrink-0" aria-hidden="true">
                  <path d="M20 6 9 17l-5-5"></path>
                </svg><span>受講者向けフォローアップ教材</span></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section id="pricing" class="py-20 bg-white">
    <div class="container mx-auto px-4">
<div class="text-center mb-20 relative">
  <div class="inline-block bg-gradient-to-r from-yellow-400 via-amber-500 to-yellow-600 text-white text-sm font-semibold tracking-wider px-5 py-2 rounded-full shadow-md mb-6">
    PRICE PLAN
  </div>
  <h3 class="text-4xl md:text-5xl font-extrabold mb-4 text-gray-900 drop-shadow-sm">
    料金プラン
  </h3>
  <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto leading-relaxed">
    あなたのニーズに合わせて選べる、<br class="hidden md:block">
    シンプルで分かりやすい料金体系です。
  </p>

  <!-- 飾りライン -->
  <div class="w-24 h-1 bg-gradient-to-r from-amber-400 to-yellow-500 mx-auto mt-6 rounded-full"></div>

  <!-- 背景装飾（ぼかしの光）-->
  <div class="absolute inset-0 -z-10 opacity-30 blur-3xl bg-gradient-to-br from-yellow-100 via-white to-amber-200"></div>
</div>


      <div class="bg-gray-50 p-8 rounded-lg shadow-md mb-12 max-w-3xl mx-auto">
        <h4 class="text-3xl font-bold text-gray-800 mb-4 text-center">【安心のプリペイド式（5,000円〜）】</h4>
        <p class="text-lg text-gray-700 mb-6 text-center">使う分だけクレジットを購入可能。料金は以下のとおり：</p>
        <ul class="space-y-4 text-gray-800 text-lg">
          <li class="flex items-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check h-6 w-6 text-green-500 mr-3 flex-shrink-0" aria-hidden="true">
              <path d="M20 6 9 17l-5-5"></path>
            </svg><span class="font-bold">5,000円 = 40クレジット</span><span class="ml-2">（実質：20分の動画生成が可能）</span></li>
          <li class="flex items-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check h-6 w-6 text-green-500 mr-3 flex-shrink-0" aria-hidden="true">
              <path d="M20 6 9 17l-5-5"></path>
            </svg><span class="font-bold">10,000円 = 100クレジット</span><span class="ml-2">（実質：50分の動画生成が可能）</span></li>
          <li class="flex items-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check h-6 w-6 text-green-500 mr-3 flex-shrink-0" aria-hidden="true">
              <path d="M20 6 9 17l-5-5"></path>
            </svg><span class="font-bold">30,000円 = 400クレジット</span><span class="ml-2">（実質：400分の動画生成が可能）</span></li>
        </ul>
      </div>

      <div class="bg-blue-50 p-8 rounded-lg shadow-md mb-12 max-w-3xl mx-auto text-center">
        <h4 class="text-3xl font-bold text-blue-700 mb-4">【まずは無料お試し】</h4>
        <p class="text-lg text-gray-700 mb-6">最大5分のあべむつき動画が無料で作成できます</p>
        <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 whitespace-nowrap font-medium transition-all disabled:pointer-events-none disabled:opacity-50 shadow-xs h-10 rounded-md bg-blue-600 hover:bg-blue-700 text-lg px-8 py-4 text-white">無料でAIあべむつきを始める</a>
      </div>

      <div class="bg-purple-50 p-8 rounded-lg shadow-md max-w-3xl mx-auto text-center">
        <h4 class="text-3xl font-bold text-purple-700 mb-4">【あべラボ参加でさらにお得】</h4>
        <p class="text-lg text-gray-700 mb-6">今なら期間限定で、オンラインサロン「あべラボ」に参加すると<br>毎月利用可能なクレジットを10分分（20クレジット）プレゼント！</p>
        <a href="https://abe-labo.biz/pages/landing/" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-2 whitespace-nowrap font-medium transition-all disabled:pointer-events-none disabled:opacity-50 shadow-xs h-10 rounded-md bg-purple-600 hover:bg-purple-700 text-lg px-8 py-4 mb-4 text-white">AIあべむつき特典付きあべラボの参加はこちらから</a>
        <p class="text-sm text-gray-600 mb-4">※ クレジット特典は上記ページからのお申し込み限定です。</p>
        <p class="text-lg text-gray-700">さらに！<br>あべラボ入会特典として、限定のあべむつきアバターも利用可能。</p>
      </div>
    </div>
  </section>
  <section class="py-20 bg-gradient-to-r from-blue-600 to-purple-600 text-white">
    <div class="container mx-auto px-4">
      <div class="text-center mb-16">
        <h3 class="text-4xl font-bold mb-4">AIあべむつきを選ぶ理由</h3>
        <p class="text-xl opacity-90 max-w-2xl mx-auto">従来の動画制作の常識を覆す、革新的なメリット</p>
      </div>
      <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div class="text-center">
          <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check h-8 w-8" aria-hidden="true">
              <path d="M20 6 9 17l-5-5"></path>
            </svg>
          </div>
          <h4 class="text-lg font-semibold">時間とコストの大幅削減</h4>
        </div>
        <div class="text-center">
          <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check h-8 w-8" aria-hidden="true">
              <path d="M20 6 9 17l-5-5"></path>
            </svg>
          </div>
          <h4 class="text-lg font-semibold">一貫したブランドイメージ</h4>
        </div>
        <div class="text-center">
          <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check h-8 w-8" aria-hidden="true">
              <path d="M20 6 9 17l-5-5"></path>
            </svg>
          </div>
          <h4 class="text-lg font-semibold">表現の幅が広がる</h4>
        </div>
        <div class="text-center">
          <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check h-8 w-8" aria-hidden="true">
              <path d="M20 6 9 17l-5-5"></path>
            </svg>
          </div>
          <h4 class="text-lg font-semibold">誰でもクリエイターに</h4>
        </div>
      </div>
    </div>
  </section>
  <section class="py-20 bg-gradient-to-r from-blue-600 to-purple-600 text-white">
    <div class="container mx-auto px-4 text-center">
      <h3 class="text-4xl font-bold mb-4">今すぐAIあべむつきを体験しよう</h3>
      <p class="text-xl opacity-90 mb-8 max-w-2xl mx-auto">無料プランで、AIあべむつきの魅力を実際に体感してください</p>
      <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 whitespace-nowrap font-medium transition-all disabled:pointer-events-none disabled:opacity-50 shadow-xs h-10 rounded-md bg-white text-blue-600 hover:bg-gray-100 text-lg px-8 py-4">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-play mr-2 h-5 w-5" aria-hidden="true">
          <polygon points="6 3 20 12 6 21 6 3"></polygon>
        </svg>無料で始める
      </a>
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