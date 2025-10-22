<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            台本作成方法ガイド
        </h2>
    </x-slot>

<div class="py-6">
    <div class="max-w-2xl mx-auto mt-10 p-6 bg-white rounded shadow">



        <p class="mb-4 leading-relaxed">
            ChatGPTを使って、<b>AIあべむつき用の台本（セリフのみ）</b>を自動生成するためのプロンプトを用意しました。<br>
            以下のプロンプトをChatGPTにコピペして使ってください。<br>

            <b>実行（エンター）する前に、プロンプトの以下の部分の【】の中身を自分のテーマに合わせて書き換えてください。</b>
        </p>

        <div class="mt-4 bg-gray-50 border rounded p-3 text-sm">
            テーマ：【ここに作りたいテーマ（例：AI関連ニュース／Sora2／副業）】<br>
            対象者：【誰向けか（例：AI初心者向け／クリエイター／ビジネス層）】<br>
            目標尺：【目安の秒数（例：240秒）】
        </div>

        <br />
        <h3 class="text-lg font-bold mb-4">AIあべむつき 台本作成プロンプト</h3>
以下をコピペしてご利用ください。
        <div class="relative mt-2">
            <button type="button" onclick="copyPrompt(event)" class="absolute top-2 right-2 flex items-center gap-1 px-2 py-1 bg-white border border-gray-300 rounded shadow hover:bg-gray-50 text-xs text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <rect x="6" y="6" width="12" height="12" rx="2" fill="#e0e7ff" stroke="#6366f1" stroke-width="2" />
                    <path d="M9 9V7a2 2 0 012-2h6a2 2 0 012 2v6a2 2 0 01-2 2h-2" stroke="#6366f1" stroke-width="2" />
                </svg>
                <span class="copy-status">コードをコピーする</span>
            </button>
            <pre id="promptPre" class="bg-gray-100 rounded p-3 text-sm whitespace-pre-wrap">
あなたはYouTubeチャンネル「AIあべむつき」の脚本家です。
指定されたテーマに沿って、最新のAI関連ニュースを調査・要約し、
あべむつき本人が“ひとりで語る”ナレーション台本を作成してください。

テーマ：【AI関連ニュース】
対象者：【AI初心者向け】
目標尺：【240秒】

# 台本作成ルール

##【目的】
- 「@{{テーマ}}」に関する最新のAIトピックを中心にまとめる。
- 出力は“セリフ本文のみ”。話者名・見出し・箇条書き・注釈・ソース・URLの表示は禁止。
- 構成：①導入 → ②ニュース3〜5本（事実＋所感） → ③まとめ（締めの一言）。
- 導入は必ず「みなさんこんにちは、AIあべむつきです！」から始める。
- 結びは必ず「それではまた」で終える。
- 口調：やわらかく落ち着いたテンポ、知的でフレンドリー。
- 想定視聴者：@{{対象者}}（例：一般層／クリエイター／ビジネス層）
- 目標尺：@{{目標尺}} 秒（±10%以内）。
- 文と文の間に自然な改行を入れ、録音しやすいリズムにする。
- 煽り・誇張・断定を避け、事実関係に基づく自然な語り口にする。

##【台本構成の指針】
- 導入：テーマ紹介と期待感を穏やかに提示。
- 本編：各ニュースを「概要 → どこが新しいか → どんな影響があるか」で構成。
- まとめ：「今回の話題を一言で振り返る＋次回への期待や感想」で自然に締める。
- ニュース例：「@{{テーマ}}に関連するAI技術／企業発表／研究成果／トレンド」など。

【禁止事項】
- 「ニュース1つ目」「ニュース2つ目」などの見出しを入れない。</pre>
        </div>



        <script>
            function copyPrompt(event) {
                event.preventDefault();
                event.stopPropagation();

                const pre = document.getElementById('promptPre');
                if (!pre) return;
                const text = pre.innerText.trim();

                const btn = event.currentTarget;
                const status = btn.querySelector('.copy-status');

                // ✅ clipboard API（https環境）
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(() => {
                        showCopySuccess(status, btn);
                    }).catch(() => {
                        fallbackCopy(text, status, btn);
                    });
                } else {
                    // ✅ HTTP環境や旧ブラウザ向けフォールバック
                    fallbackCopy(text, status, btn);
                }
            }

            function fallbackCopy(text, status, btn) {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                document.body.appendChild(textarea);
                textarea.focus();
                textarea.select();

                try {
                    const successful = document.execCommand('copy');
                    if (successful) {
                        showCopySuccess(status, btn);
                    } else {
                        alert('コピーに失敗しました（ブラウザ制限）');
                    }
                } catch (err) {
                    alert('コピーに失敗しました');
                } finally {
                    document.body.removeChild(textarea);
                }
            }

            function showCopySuccess(status, btn) {
                if (!status) return;
                const original = status.innerText;
                status.innerText = 'コピーしました！';
                status.classList.add('text-green-600', 'font-semibold');
                btn.classList.add('bg-indigo-50', 'border-indigo-400');

                setTimeout(() => {
                    status.innerText = original;
                    status.classList.remove('text-green-600', 'font-semibold');
                    btn.classList.remove('bg-indigo-50', 'border-indigo-400');
                }, 1500);
            }
        </script>



        <div class="mt-6 text-sm text-gray-600">
            <p>このガイドに沿って台本を作成し、音声生成フォームに貼り付けるだけで、AIあべむつきの声でナレーションが作成できます。</p>
        </div>

        <div class="py-6">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            音声が上手く作れないときは？
        </h2>
        <div class="mt-4 space-y-4">
            <p>
                英単語を上手く話してくれない場合は、単語をひらがなやカタカナで表記して音声AIが読みやすく整えてください。
                <br>
                例：
                <ul class="list-disc pl-5">
                    <li>ChatGPT ➔ チャットジーピーティー</li>
                    <li>Sora2 ➔ ソラツー</li>
                </ul>
            </p>
            <p>
                日本語でも読みが不自然な場合、文章を変更するなどで自然な読みになるよう調整してください。
                <br>
                例：
                <ul class="list-disc pl-5">
                    <li>インターネット利用がより快適に！ ➔ インターネットの利用がより快適になります！</li>
                </ul>
            </p>
        </div>
        </div>

    </div>
    </div>

</x-app-layout>