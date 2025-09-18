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




