# 会員登録時のクーポンコード連動 初期クレジット付与機能（code='' デフォルト方式）

## 概要
会員登録フォームで入力されたクーポンコードに応じて、初期クレジットを付与する機能です。

- クーポンコードが入力された場合：一致するレコードを検索し、**期間内** かつ **is_active=1** なら `credits` を付与。
- クーポンコードが未入力の場合：`code=''` の行（デフォルト）を参照して付与。
- 対象レコードが存在しない / 期間外 / 無効 の場合は付与しません。
- 付与結果は `credit_histories` に1件記録します（`reason` などのメモを残す）。

---

## テーブル仕様

### 名称
`coupon_initial_credits`

### カラム構成
| カラム名 | 型 | 概要 |
|---|---|---|
| `id` | BIGINT PK | 自動採番 |
| `code` | VARCHAR(64) NOT NULL DEFAULT '' | クーポンコード（空文字＝未入力デフォルト） |
| `credits` | INT UNSIGNED | 付与クレジット数 |
| `starts_at` | DATETIME NULL | 有効開始日時（NULL=制限なし） |
| `ends_at` | DATETIME NULL | 有効終了日時（NULL=制限なし） |
| `is_active` | BOOLEAN DEFAULT 1 | 有効/無効フラグ |
| `note` | VARCHAR(255) NULL | 管理メモ |
| `created_at` / `updated_at` | timestamp | 自動生成 |

### 制約
```php
// 空文字を含め code は一意
$t->unique('code');
```

### マイグレーション（例）
```php
// database/migrations/2025_10_06_000000_create_coupon_initial_credits_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('coupon_initial_credits', function (Blueprint $t) {
            $t->id();
            $t->string('code', 64)->default(''); // 未入力は空文字
            $t->unsignedInteger('credits');
            $t->dateTime('starts_at')->nullable();
            $t->dateTime('ends_at')->nullable();
            $t->boolean('is_active')->default(true);
            $t->string('note', 255)->nullable();
            $t->timestamps();
            $t->unique('code');
        });

        DB::table('coupon_initial_credits')->insert([
            'code'       => '',
            'credits'    => 0,
            'is_active'  => true,
            'note'       => '登録時デフォルト',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void {
        Schema::dropIfExists('coupon_initial_credits');
    }
};
```

---

## モデル

**app/Models/CouponInitialCredit.php**
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponInitialCredit extends Model
{
    protected $fillable = [
        'code','credits','starts_at','ends_at','is_active','note',
    ];

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeInWindow($q)
    {
        $now = now();
        return $q->where(function ($q) use ($now) {
            $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
        })->where(function ($q) use ($now) {
            $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
        });
    }
}
```

---

## サービス層

**app/Services/InitialCreditResolver.php**
```php
namespace App\Services;

use App\Models\CouponInitialCredit;

class InitialCreditResolver
{
    /**
     * クーポンコードに対応する初期クレジット設定を取得します。
     *
     * @param string|null $code ユーザー入力クーポンコード（未入力可）
     * @return CouponInitialCredit|null
     */
    public function resolve(?string $code): ?CouponInitialCredit
    {
        $code = trim($code ?? '');

        return CouponInitialCredit::query()
            ->active()
            ->inWindow()
            ->where('code', $code)
            ->first();
    }
}
```

---

## 登録フロー組み込み（例）

### イベント＆リスナ
- `App\Events\UserRegistered`（既に `Registered` を使っている場合はそれでOK）
- `App\Listeners\GrantInitialCreditAfterRegister`

**app/Listeners/GrantInitialCreditAfterRegister.php**
```php
namespace App\Listeners;

use App\Services\InitialCreditResolver;
use App\Services\CreditService;

class GrantInitialCreditAfterRegister
{
    public function __construct(
        protected InitialCreditResolver $resolver,
        protected CreditService $creditService,
    ) {}

    public function handle($event): void
    {
        $user = $event->user;

        // フォーム名に合わせて取得。無ければ空文字。
        $couponCode = request()->string('coupon_code')->toString() ?: '';

        $coupon = $this->resolver->resolve($couponCode);

        if ($coupon && $coupon->credits > 0) {
            $this->creditService->grant(
                user: $user,
                credits: $coupon->credits,
                reason: 'signup:coupon:' . ($coupon->code ?: 'default')
            );
        }
    }
}
```

---

## クレジット履歴の保存（例）

`CreditService::grant()` 内で `credit_histories` に追記する想定：
```php
CreditHistory::create([
    'user_id'    => $user->id,
    'granted_at' => now(),
    'credit'     => $credits,
    'note'       => $reason, // 例: signup:coupon:WELCOME
]);
```

---

## 管理 UI（Filament 推奨）
- `coupon_initial_credits` の CRUD を作成して運用。
- **運用ルール**：`code=''`（デフォルト行）は1件のみ。必要に応じて `credits` を編集して既定値を変更。

---

## 仕様サマリ

| 状況 | 動作 |
|---|---|
| 入力コード一致 + 期間内 + is_active=1 | 対応 `credits` を付与 |
| 未入力（空） | `code=''` 行を参照して付与 |
| 期間外 / 無効 / レコードなし | 付与なし（履歴もなし） |

---

## テスト観点
- code='WELCOME' が期間内：正しい付与と履歴 1 行。
- code='OLD' が期間外：付与スキップ、履歴なし。
- code='' デフォルト=50：50 付与と履歴 1 行。
- code='UNKNOWN'：付与スキップ、履歴なし。

---

## 補足
- タイムゾーンは `config/app.php` に従う。
- 将来の拡張（任意）：`usage_limit`、`consumed_count`、`last_used_at` 等でクーポンの使用回数を制御可能。




# クーポンコード利用履歴テーブル（coupon_logs）

## 概要
このテーブルは、ユーザーがクーポンコードを使用した履歴を保持し、  
再入会や複数アカウントによる **同一メールでの重複利用を防止** するための仕組みです。

Google 認証経由で取得されるメールアドレスは既に正規化済みで一意性が保証されているため、  
追加の正規化処理（ドット除去・+タグ除去など）は不要です。

---

## テーブル仕様

### テーブル名
`coupon_logs`

### カラム構成

| カラム名 | 型 | 概要 |
|-----------|------|------|
| `id` | BIGINT PK | 自動採番 |
| `code` | VARCHAR(64) NOT NULL | 使用されたクーポンコード |
| `email` | VARCHAR(191) NOT NULL | Google認証で取得したユーザーのメールアドレス |
| `user_id` | BIGINT NULL | 当時のユーザーID（削除されてもNULLで残す） |
| `ip` | VARCHAR(45) NULL | 利用元IP（任意） |
| `user_agent` | VARCHAR(255) NULL | 利用端末（任意） |
| `note` | VARCHAR(255) NULL | 管理用メモ |
| `created_at` / `updated_at` | timestamp | 自動生成 |

### 制約・インデックス
```php
$t->unique(['code', 'email']); // 同一メールで同一クーポンの再利用を禁止
$t->index('email');
$t->index('code');
```php


### マイグレーション例
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('coupon_redemptions', function (Blueprint $t) {
            $t->id();
            $t->string('code', 64);
            $t->string('email', 191);
            $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('ip', 45)->nullable();
            $t->string('user_agent', 255)->nullable();
            $t->string('note', 255)->nullable();
            $t->timestamps();

            $t->unique(['code', 'email']);
            $t->index('email');
            $t->index('code');
        });
    }

    public function down(): void {
        Schema::dropIfExists('coupon_redemptions');
    }
};
