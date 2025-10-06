# bladeの実装方針
すべての Blade テンプレートは Jetstream/Breeze 形式で統一してください。
`@extends` や `@section` は使わず、常に `<x-app-layout>` を使用して書いてください。

コンテンツ部分は `<x-slot name="header">` と `<x-slot name="content">` を使う構成でお願いします。



# Infotop 課金クレジット付与機能

この機能は **インフォトップ決済の売上データ** をもとに、ユーザーにクレジットを自動付与します。  
定期実行（Job／スケジューラ）と、管理画面（Filament）からの手動実行の両方に対応します。  
処理は共通サービス `InfotopCreditGrantService` に集約します。

---

## テーブル構造

利用するテーブル：`credit_histories`

| カラム       | 型           | 制約                     | 説明 |
|--------------|-------------|--------------------------|------|
| id           | bigint      | PK, AUTO_INCREMENT       | 主キー |
| user_id      | bigint      | NOT NULL                 | クレジット付与対象ユーザー |
| order_id     | varchar(255)| UNIQUE, NOT NULL         | インフォトップ注文ID（二重付与防止） |
| amount       | int         | NOT NULL                 | 決済金額 |
| credit       | int         | NOT NULL                 | 付与クレジット数 |
| system       | varchar(255)| NOT NULL                 | `infotop` 固定 |
| granted_at   | timestamp   | DEFAULT CURRENT_TIMESTAMP| 付与日時 |
| note         | varchar(255)| NULLABLE                 | 補足メモ |
| created_at   | timestamp   |                          | Laravel標準 |
| updated_at   | timestamp   |                          | Laravel標準 |

---

## 実装方針

### 1. 共通サービス
- `App\Services\InfotopCreditGrantService`
- 責務：
  - データ取得（Google Sheets API or `_docs/infotop_sales.tsv`）
  - 行ごとの正規化（注文ID、金額、ユーザー特定）
  - 金額 → クレジット変換
  - `credit_histories` に保存（`order_id` UNIQUE で二重付与防止）

### 2. Job
- `App\Jobs\ImportInfotopSalesJob`
- キュー実行可能
- サービスを呼び出して実行
- 実行結果をログ／通知

### 3. Artisan コマンド
- `php artisan infotop:import`
- Job を直接呼ばず、サービスを呼び出す
- 運用・検証用

### 4. スケジューラ
- `app/Console/Kernel.php` に登録
- 例：15分おきに実行
```php
$schedule->job(new ImportInfotopSalesJob)->everyFifteenMinutes();
5. Filament 管理画面ボタン
Filament のカスタムページ or Widget に「インフォトップ売上取込」ボタンを設置

押下で Job を dispatch

成功／失敗を Toast 通知表示

設定
.env でデータ取得ソースを切り替え

env
コードをコピーする
INFOTOP_SOURCE=sheet   # or file
INFOTOP_SHEET_ID=18pHbkmTbF7SPK6CYwqV2eXM2gCxQUNy_MXqbJT4myJM
INFOTOP_SHEET_RANGE="シート1!A:Z"
GOOGLE_APPLICATION_CREDENTIALS=/path/to/service_account.json
注意事項
order_id は UNIQUE 制約で二重付与を防止する

Job 多重実行を避けるために Cache::lock を使う

金額 → クレジット変換ロジックはサービス内 amountToCredit() に実装する

ユーザー特定ロジックは resolveUserId() で調整する（メールアドレス等で紐付け）