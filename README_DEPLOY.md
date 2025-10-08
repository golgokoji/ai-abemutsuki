# AIあべむつき CoreServer デプロイ手順

本書は、Laravel＋Docker構成の「AIあべむつき」を **CoreServer 上のサブドメイン（ai.abe-labo.biz）** に設置・稼働させるための手順をまとめたものです。

---

## 🚀 構成概要

| 項目 | 内容 |
|------|------|
| サーバー | CoreServer（共用） |
| ドメイン | abe-labo.biz |
| サブドメイン | ai.abe-labo.biz |
| 公開ディレクトリ | `/home/ユーザー名/domains/ai.abe-labo.biz/public` |
| PHP | 8.3 以上 |
| Node.js | 18 以上 |
| DB | MySQL 8 |
| フレームワーク | Laravel 11 |
| 管理UI | Filament |
| ソース | [GitHub: golgokoji/ai-abemutsuki](https://github.com/golgokoji/ai-abemutsuki) |

---

## 1️⃣ CoreServer 側の初期設定

1. CoreServerコントロールパネルにログイン  
2. 「ドメイン設定」→「サブドメイン追加」  
   - サブドメイン名: `ai`
   - 公開ディレクトリ: `domains/ai.abe-labo.biz/public`
3. SSL設定（Let’s Encrypt）を有効化  
   - チェックを入れて発行  
   - 発行対象に `abe-labo.biz`, `ai.abe-labo.biz` を含める  
   - 成功メッセージ例：

     ```
     LetsEncrypt request successful for:
     abe-labo.biz
     ai.abe-labo.biz
     www.abe-labo.biz
     ```

   - 発行後、5〜10分ほど待機して反映を確認

---


## 2️⃣ ソースコードの配置（Git方式推奨）

CoreServerにSSHログインし、以下を実行します。

```bash
cd ~/domains/ai.abe-labo.biz
git clone https://github.com/golgokoji/ai-abemutsuki.git .
```

既にファイルがある場合は、上書きの前にバックアップを取ってください。  
`.env` は後述の手順で別途設置します。

---

## 3️⃣ `.env` 設定

`domains/ai.abe-labo.biz/.env` に以下を設置：

```dotenv
APP_NAME="AIあべむつき"
APP_ENV=production
APP_KEY=base64:xxxxxxxxxxxxxxxxx
APP_DEBUG=false
APP_URL=https://ai.abe-labo.biz

LOG_CHANNEL=stack
LOG_LEVEL=info

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=ai_abemutsuki
DB_USERNAME=ユーザー名
DB_PASSWORD=パスワード

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database

FILESYSTEM_DISK=public
```

`.env` はリポジトリに含めず、CoreServer上でのみ配置してください。

---

## 4️⃣ 権限設定

```bash
cd ~/domains/ai.abe-labo.biz
chmod -R 755 storage bootstrap/cache
chmod -R 777 storage/logs
```

---

## 5️⃣ Composer & Build

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
npm install
npm run build
```

---

## 6️⃣ キャッシュ最適化

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 7️⃣ 動作確認

ブラウザでアクセス：

👉 [https://ai.abe-labo.biz](https://ai.abe-labo.biz)

LaravelのトップページまたはFilamentログイン画面が表示されれば成功。

---

## 8️⃣ 管理画面ログイン

Filament 管理画面URL：

```
https://ai.abe-labo.biz/admin
```

初回ログイン用ユーザーを作成：

```bash
php artisan make:filament-user
```

---

## 9️⃣ 更新手順

```bash
cd ~/domains/ai.abe-labo.biz
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm run build
php artisan cache:clear
```

---

## 🔧 メンテナンスモード

```bash
php artisan down
php artisan up
```

---

## 💬 トラブルシューティング

| 症状 | 対応方法 |
|------|----------|
| 500 Internal Server Error | `.env` のAPP_KEY設定またはstorage権限を確認 |
| 404 Not Found | `.htaccess` が `public/` 直下にあるか確認 |
| CSS/JSが反映されない | `npm run build` → `php artisan view:clear` |
| Migration失敗 | DBユーザー権限と接続情報を再確認 |

---

## 🧰 補足メモ

- CoreServerは常駐プロセスが制限されているため、`queue:work` は外部サーバーで管理推奨  
- cronが使える場合は、以下のように設定可能：

  ```bash
  * * * * * /usr/bin/php /home/ユーザー名/domains/ai.abe-labo.biz/artisan schedule:run >> /dev/null 2>&1
  ```

- メール送信は `mailgun` または `sendgrid` 推奨

---

## 🧾 バージョン情報

| コンポーネント | バージョン例 |
|----------------|---------------|
| Laravel | 11.x |
| PHP | 8.3 |
| Node | 18.x |
| MySQL | 8.x |
| Filament | 3.x |

---

## 🏷️ 管理者メモ

- ソースリポジトリ: [https://github.com/golgokoji/ai-abemutsuki](https://github.com/golgokoji/ai-abemutsuki)
- 管理画面URL: `https://ai.abe-labo.biz/admin`
- データベース: `ai_abemutsuki`
- メールテスト用: Mailtrap または MailHog 利用可
- 外部ジョブ処理: Google Cloud / Render / CronJob.org 等を併用可能

---

© Lucky Mine / AIあべむつき  
All Rights Reserved.
