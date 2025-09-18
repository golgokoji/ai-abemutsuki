<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('status'))
                        <div id="flash-message" class="mb-4 p-3 bg-green-100 border-l-4 border-green-400 text-green-800 relative">
                            <span>{{ session('status') }}</span>
                            <button type="button" onclick="document.getElementById('flash-message').style.display='none'" class="absolute top-2 right-2 text-green-800 hover:text-green-600 text-xl font-bold focus:outline-none">&times;</button>
                        </div>
                    @endif
                    <h2 class="text-xl font-semibold mb-6">あべラボ連動 登録情報</h2>
                    <div class="mb-6 p-4 bg-gray-100 border-l-4 border-blue-300 text-gray-700">
                        <strong>注意：</strong>決済システムと連動させるために、<br>
                        <span class="font-bold">あべラボ入会時（決済時）の情報</span>を必ず正確に入力してください。<br>
                        （会員情報とは異なる場合があります）<br>
                    </div>
                    <form method="POST" action="#">
                        @csrf
                        <div class="mb-4">
                            <label for="abelabo_name" class="block font-medium text-sm text-gray-700">氏名</label>
                            <input id="abelabo_name" type="text" name="abelabo_name" value="{{ old('abelabo_name', isset($abelabo) ? $abelabo->name : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div class="mb-4">
                            <label for="abelabo_email" class="block font-medium text-sm text-gray-700">メールアドレス</label>
                            <input id="abelabo_email" type="email" name="abelabo_email" value="{{ old('abelabo_email', isset($abelabo) ? $abelabo->email : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            @error('abelabo_email')
                                <div class="text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="abelabo_tel" class="block font-medium text-sm text-gray-700">電話番号</label>
                            <input id="abelabo_tel" type="text" name="abelabo_tel" value="{{ old('abelabo_tel', isset($abelabo) ? $abelabo->tel : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <!-- 必要に応じて追加カラム -->
                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                保存
                            </button>
                        </div>
                    </form>

                    <div class="mb-6 p-4 bg-gray-100 border-l-4 border-blue-300 text-gray-700">
                        会員情報がわからないなど、ご不明な点は以下のLINEまでお問い合わせください。<br>
                        <a href="http://abe-labo.biz/r/line_sougou" class="underline text-blue-700" target="_blank">http://abe-labo.biz/r/line_sougou</a><br>
                        問い合わせの際は、およその入会日、お名前など分かる範囲で情報を添えていただけると対応がスムーズです。
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
