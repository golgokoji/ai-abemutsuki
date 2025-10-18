
<x-guest-layout>
    <x-slot name="title">サポート</x-slot>
    <div class="container mx-auto px-4 py-12 max-w-2xl">
      <h1 class="text-3xl font-bold mb-8 text-center">サポート・お問い合わせ</h1>

      <div class="bg-white rounded-lg shadow-md p-8 mb-8">
        <h2 class="text-xl font-semibold mb-4">メールでのお問い合わせ</h2>
        <p class="mb-2">ご質問・ご要望・不具合報告などは、下記メールアドレスまでご連絡ください。</p>
        <p class="font-mono text-blue-700 text-lg break-all mb-4">support@lucky-mine.com</p>
        <p class="font-mono text-blue-700 text-lg break-all mb-4">luckymine23@gmail.com（予備アドレス）</p>
        <p class="text-sm text-gray-500">※ 通常1〜2営業日以内にご返信いたします。</p>
      </div>

      <div class="bg-white rounded-lg shadow-md p-8 flex flex-col sm:flex-row items-center gap-6">
        <div class="flex-1">
          <h2 class="text-xl font-semibold mb-4">LINEでのお問い合わせ</h2>
          <p class="mb-2">LINE公式アカウントではAIによる２４時間自動サポートも受け付けています。</p>
          <a href="https://lin.ee/DQKqxPG" target="_blank" rel="noopener noreferrer" class="inline-block mt-2 px-6 py-3 bg-green-500 text-white rounded-md font-bold hover:bg-green-600 transition">LINEで問い合わせる</a>
        </div>
        <div class="flex-shrink-0">
          <img src="/images/support_line.png" alt="LINEサポートQRコード" class="w-48 h-48 object-contain border rounded-md shadow" />
          <div class="text-xs text-gray-500 mt-2 text-center">QRコードからも友だち追加できます</div>
        </div>
      </div>
    </div>
</x-guest-layout>
