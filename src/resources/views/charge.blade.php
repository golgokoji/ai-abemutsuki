

<x-app-layout>
    <div class="max-w-md mx-auto mt-10 p-8 bg-white rounded shadow">
        <h2 class="text-2xl font-bold mb-6 text-indigo-700">クレジットチャージ</h2>
        <p class="mb-8 text-gray-700">AIあべむつきの動画生成に使えるクレジットを購入できます。<br>ご希望のプランを選択してください。</p>
        <form id="chargeForm" class="space-y-6" method="GET" action="#" onsubmit="return goToPayment();">
            <div>
                <label for="plan" class="block font-semibold mb-2">プラン選択</label>
                <select id="plan" name="plan" class="w-full border rounded px-3 py-2">
                    <option value="" disabled selected>プランを選択してください</option>
                    <option value="pd_yw68pieicewcio1k">20クレジット（5000円）／約10分</option>
                    <option value="pd_zl1xbrox3fqgvjnx">50クレジット（10000円）／約25分</option>
                    <option value="pd_ycozmxi27h0qekgb">200クレジット（30000円）／約100分</option>
                </select>
            </div>
            <button type="submit" class="w-full py-3 px-4 rounded bg-indigo-600 text-white font-bold text-lg shadow hover:bg-indigo-700 transition">決済ページへ</button>
        </form>
        <script>
        function goToPayment() {
            const plan = document.getElementById('plan').value;
            if (!plan) return false;
            const urls = {
                'pd_yw68pieicewcio1k': 'https://order.payz.jp/products/pd_yw68pieicewcio1k/purchases/new',
                'pd_zl1xbrox3fqgvjnx': 'https://order.payz.jp/products/pd_zl1xbrox3fqgvjnx/purchases/new',
                'pd_ycozmxi27h0qekgb': 'https://order.payz.jp/products/pd_ycozmxi27h0qekgb/purchases/new',
            };
            window.open(urls[plan], '_blank');
            return false;
        }
        </script>
    </div>
</x-app-layout>
