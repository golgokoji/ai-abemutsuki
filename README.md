# Artisanの実行について
マイグレーションなどのコマンドはdocker上で実行します。
その前提でコマンドを教えて下さい。

# AI-abemutsuki — Copilot 引き継ぎメモ

## 0. 目的（Done の定義）
- 生成した**音声**を **S3(voices/)** に保存し、その**公開URL**で **HeyGen API** に発注。
- 返ってきた **video_id** をポーリングして完成したら、**動画をダウンロード → S3(videos/)** に保存。
- 生成ジョブと結果を **DB**（`videos` テーブル）に保存・更新できること。
- CLI（Artisan）から一連の処理を実行できること。

---

## 1. 環境 / 前提
- AWS S3  
  - バケット: `ai-abemutsuki`（リージョン: `ap-southeast-2`）  
  - 公開設定: **バケットのパブリックブロックはOFF**。  
  - バケットポリシーで **`voices/*` と `videos/*` のみ匿名GET許可**（最小公開）。
    ```json
    {
      "Version":"2012-10-17",
      "Statement":[
        {"Sid":"PublicReadVoices","Effect":"Allow","Principal":"*","Action":"s3:GetObject","Resource":"arn:aws:s3:::ai-abemutsuki/voices/*"},
        {"Sid":"PublicReadVideos","Effect":"Allow","Principal":"*","Action":"s3:GetObject","Resource":"arn:aws:s3:::ai-abemutsuki/videos/*"}
      ]
    }
    ```
- Laravel `config/filesystems.php` の s3 ディスクは `visibility => 'public'` 推奨。  
- `.env`（例）
  ```
  AWS_ACCESS_KEY_ID=xxx
  AWS_SECRET_ACCESS_KEY=xxx
  AWS_DEFAULT_REGION=ap-southeast-2
  AWS_BUCKET=ai-abemutsuki
  AWS_URL=https://ai-abemutsuki.s3.ap-southeast-2.amazonaws.com

  # HeyGen
  HEYGEN_API_KEY=（※暫定。users.heygen_api_key 優先でもOK）
  HEYGEN_AVATAR_ID=（例: Monica_inSleeveless_20220819）
  FIXED_AUDIO_URL=https://ai-abemutsuki.s3.ap-southeast-2.amazonaws.com/voices/sample.mp3
  ```

---

## 2. DB スキーマ（存在前提 / 変更があれば合わせて）
- `users`：`heygen_api_key` カラムあり（true を確認済み）
- `videos` テーブル（想定カラム）
  - `id, user_id, heygen_video_id, status, video_url, request_payload(json), last_response(json), timestamps`
- Eloquent リレーション
  - `User hasMany Video` / `Video belongsTo User`

---

## 3. 主要フロー（擬似コード）
```
input: user_id (=1 で固定でも可), avatar_id, (audio_source)

# (A) 音声のS3保存
if audio_source is local bytes:
    s3.put("voices/{uuid}.mp3", binary, public)
    audio_url = "{AWS_URL}/voices/{uuid}.mp3"
else:
    audio_url = FIXED_AUDIO_URL or provided_url   # まずは固定でOK

# (B) HeyGen へ発注
api_key = users.heygen_api_key(user_id) or env(HEYGEN_API_KEY)
payload = {
  video_inputs: [{
    character: { type:"avatar", avatar_id, avatar_style:"normal" },
    voice: { type:"audio", audio_url },
    background: { type:"color", value:"#000000" }
  }],
  dimension: { width:1280, height:720 }
}
res = POST https://api.heygen.com/v2/video/generate  (X-Api-Key)
video_id = res.data.video_id

# (C) videos テーブルに初期保存
Video::create({
  user_id, heygen_video_id: video_id, status:"processing",
  video_url: null, request_payload: payload, last_response: res.json
})

# (D) ポーリング → 完了でDL
loop until completed/failed or timeout:
  st = GET https://api.heygen.com/v1/video_status.get?video_id=...
  if st.data.status == "completed":
      bin = GET st.data.video_url (expires)
      s3.put("videos/{video_id}.mp4", bin, public)
      public_url = "{AWS_URL}/videos/{video_id}.mp4"
      Video::where(heygen_video_id=video_id)->update({
        status:"completed", video_url: public_url, last_response: st.json
      })
      break
  elif st.data.status in ["failed","canceled"]:
      update status accordingly; store last_response; break
```

---

## 4. 既存CLI（Artisan）コマンドの方針
- コマンド名：`heygen:test`  
- 役割：**固定URL**での動画生成テスト → 完了後に S3 へ動画保存 → `videos` 更新。  
- オプション：`--interval`（デフォ5秒）, `--max`（デフォ60回）  
- 改修ポイント（Copilot へ依頼）  
  1) 完了時に **S3アップロード & DB更新** を確実に行う。  
  2) `users.heygen_api_key` を優先使用（無ければ `.env` をフォールバック）。  
  3) `videos` レコードの `request_payload` と `last_response` を常に保存/更新。  
  4) 例外時は `status=failed` に更新し、レスポンス本文・HTTPコードを `last_response` に格納。  
  5) 単体関数 `uploadAudioToS3($bytes, $ext='mp3'): string` を作り、`voices/` に保存して公開URLを返す。今は未使用でもOK。

---

## 5. Copilot へ具体依頼テンプレ
> 次の変更を行ってください：
> 1. `app/Console/Commands/HeygenTest.php` にて、status=`completed` の際に
>    - `video_url` からバイナリを取得
>    - `Storage::disk('s3')->put("videos/{$videoId}.mp4", $bin, ['visibility'=>'public'])`
>    - 公開URLを `videos` テーブルに保存（`video_url` カラム）
> 2. HeyGen の API キーは `User::find(1)->heygen_api_key` を最優先で使用。無い場合のみ `env('HEYGEN_API_KEY')` を使う。
> 3. `videos` レコードの `request_payload` と `last_response` を常に保存/更新。
> 4. 例外時は `status=failed` に更新し、レスポンス本文・HTTPコードを `last_response` に格納。
> 5. 単体関数 `uploadAudioToS3($bytes, $ext='mp3'): string` を作り、`voices/` に保存して公開URLを返す。今は未使用でもOK。

---

## 6. テスト手順（手動）
1. `.env` とバケットポリシーが上記通りか確認。  
2. `php artisan heygen:test` 実行。  
3. 完了後、`videos/{video_id}.mp4` が S3 に存在し、匿名GETできること（URL直叩き）。  
4. DB `videos` 行が `status=completed` かつ `video_url` が S3 公開URLになっていること。  

---

## 7. 追加タスク（後回し可）
- Webhook駆動（ポーリング削減）：HeyGen 側の完了通知を受ける HTTP エンドポイント追加。  
- マルチユーザー化：`user_id` をコマンド引数に、APIキー/保存先を切替。  
- 透かし除去：有料アカウントの API キーに切替（`users.heygen_api_key` を運用）。  
- 再生用短縮URL or CDN：CloudFront/署名付きURLの採用。  
- 監査ログ整備：失敗時のレスポンス・再試行設計。

---

## 8. セキュリティ注意
- S3 公開は **`voices/*` と `videos/*` のみに限定**。他パスは非公開。  
- APIキーは DB に保存するが、**ログへは出さない**。  
- 例外時のレスポンス保存は **個人情報/秘匿値のマスク** を考慮。




## Blade レイアウト方針（Copilot遵守ルール）

**全てのページビューは `<x-app-layout>` を必ず使う。**  
`@extends('layouts.app')` / `@section` / `@yield` ベースや、ページ側での `$slot` 直接利用は禁止。

### ✅ 基本ルール
- ページごとの Blade は **必ず** 下記の雛形で開始する：
  ```blade
  {{-- resources/views/<feature>/index.blade.php など --}}
  <x-app-layout>
      <x-slot name="header">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
              {{ __('ページタイトル') }}
          </h2>
      </x-slot>

      {{-- ページ本体 --}}
      <div class="py-6">
          <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
              <!-- ここにコンテンツ -->
          </div>
      </div>
  </x-app-layout>


# Filament v3 CreateRecord ページでのフォームカスタマイズ方法

## 背景
- Filament v3 では、Resource ページのフォームは **`CreateRecord` / `EditRecord` の `form()` メソッド**を通じて構築されます。
- ページクラスに `getFormSchema()` を定義しても、Resource ページでは呼び出されません。  
  （`getFormSchema()` は通常の `Page` クラス用の仕組みです）

そのため、**ページごとにフォーム項目を完全にカスタマイズしたい場合は `form()` をオーバーライドする必要があります。**

---

## 実装例

```php
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateCreditHistory extends CreateRecord
{
    protected static string $resource = CreditHistoryResource::class;

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('credits')
                ->label('クレジット数')
                ->numeric()
                ->required(),

            Forms\Components\TextInput::make('amount')
                ->label('金額')
                ->numeric()
                ->required(),

            Forms\Components\Textarea::make('note')
                ->label('備考')
                ->rows(3),
        ]);
    }
}


# プロジェクト構成について

## アプリケーションルート
本プロジェクトでは、通常の Laravel 構成と異なり **`src/` 配下**がアプリケーションルートとして設定されています。  
そのため、以下のようになります。

- アプリケーション本体: `src/app/`
- 設定ファイル: `src/config/`
- ルーティング: `src/routes/`
- マイグレーション / シーダー: `src/database/`
- ビュー: `src/resources/views/`

## View のパス解決
Laravel の `view.php` 設定により、ビューは `resource_path('views')` を基準に解決されます。  
本プロジェクトでは `base_path = src/` となっているため、実際には以下のディレクトリが参照されます。

src/resources/views/


## Filament のカスタムビュー
Filament も Laravel のビュー解決に従います。  
そのため、Filament 用のカスタムビューを配置する際は以下のようにします。

例:
src/resources/views/filament/raw-html.blade.php


これにより、`view('filament.raw-html')` で参照できます。

---

## 注意点
- プロジェクト直下 (`/resources`) に存在するビューは参照されません。  
- 必ず **`src/resources/views`** 以下に配置してください。


# Filament v3 フォームボタンのラベル変更方法

## CreateRecord ページのボタンラベル

Filament v3 の `CreateRecord` ページでは、デフォルトで以下のボタンが表示されます。

- Create
- Cancel

これらのボタンは **アクションオブジェクト (`Actions\Action`)** として生成されます。  
そのため、v2 のような `getCreateFormActionLabel()` / `getCancelFormActionLabel()` は呼ばれません。

---

## 方法 1: 最小限のラベル変更

```php
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCreditHistory extends CreateRecord
{
    protected static string $resource = CreditHistoriesResource::class;

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label('クレジット追加'),
            $this->getCancelFormAction()->label('戻る'),
        ];
    }
}
