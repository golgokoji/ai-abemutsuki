# Fish Audio API 切り替えタスク

現在の音声合成処理は ElevenLabs を使用しているが、これを Fish Audio API に切り替える。

## 実装ルール
- API キー（シークレットキー）は環境変数から読み込むこと
  - `.env` に `FISH_AUDIO_API_KEY=de21189b47cd4d6aae7f927007fa659d` を設定
  - コード上では直接キーを書かず、`env('FISH_AUDIO_API_KEY')` を参照する
- API のエンドポイントは https://api.fish.audio/ を使用する
- テキストから音声を生成するエンドポイントを利用し、返却される音声ファイルを保存する仕組みにする
- 既存の音声合成関数（例：`generateVoice()`）を Fish Audio API に置き換える
- 成功時は音声データ（mp3/wav）を保存し、失敗時はエラーログを出す
- Laravel のサービスクラス（例：`app/Services/FishAudioService.php`）として実装する
- 単体テスト（例：`tests/Feature/FishAudioServiceTest.php`）も用意する

## 具体的な処理フロー
1. テキスト入力を受け取る
2. Fish Audio API に POST リクエストを送信する
   - ヘッダーに `Authorization: Bearer <API_KEY>` を含める
   - `Content-Type: application/json`
3. 返却された音声データをストレージ（`storage/app/public/voices/`）に保存
4. 保存したファイルのパスを返す


# Fish Audio API 実装タスク

## 目的
ElevenLabs から Fish Audio へ切り替える。
Web UI で調整した声質（速度1.0、温度0.9、TopP0.9、ボリューム0、モデルS1）を API 経由でも再現できるようにする。

## 実装ルール
- APIキーは `.env` に記載し、`env('FISH_AUDIO_API_KEY')` から参照
  - 例: FISH_AUDIO_API_KEY=xxxxxx
- サービスクラス `app/Services/FishAudioService.php` を作成
- テキスト入力を受け取り、Fish Audio API にリクエストを送信
- 音声ファイル（mp3）を `storage/app/public/voices/` に保存し、そのパスを返却
- エラー時は例外を投げ、ログに詳細を記録
- 設定値は `config/fishaudio.php` にまとめる
- 単体テスト `tests/Feature/FishAudioServiceTest.php` を作成

## API 情報
- エンドポイント: POST https://api.fish.audio/v1/tts
- ヘッダー:
  - Authorization: Bearer {FISH_AUDIO_API_KEY}
  - Content-Type: application/json
  - model: speech-1.5
- リクエスト body 例:
  ```json
  {
    "text": "こんにちは、AIあべむつきです。",
    "reference_id": "クローン音声ID",
    "format": "mp3",
    "latency": "balanced",
    "temperature": 0.9,
    "top_p": 0.9,
    "volume": 0,
    "speed": 1.0
  }

## プリセット

default: { model: "S1", latency: "balanced", temperature: 0.9, top_p: 0.9, volume: 0, speed: 1.0 }

quality: { latency: "quality" }

fast: { latency: "fast", speed: 1.1 }

deep: { temperature: 0.7, top_p: 0.7 }