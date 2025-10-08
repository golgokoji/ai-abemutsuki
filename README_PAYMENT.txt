payzを利用したクレジット決済の実装について

You are implementing a secure “purchase claim” flow in a Laravel app to grant user credits when they click a link from a Payz purchase email.

Context

PHP 8.x / Laravel 10+ / Docker / Auth: Google login（users.email が本人識別の唯一の公式メール）

Webhookで決済イベントを保存済み（payz_webhook_events）。

メール本文には https://ai.abe-labo.biz/charge?purchase_uid=<ID>&email=<payment_email> を記載する。

仕様：ユーザーがリンクを踏み、ログインしている Googleメール と、リンクの email、Webhook保存済みの payment_email が全て厳格一致した場合のみ付与。

Goal
次の仕様を満たすコードを追加・修正して完成させて。

1) DB（未実装なら作成）

payz_pending_grants テーブルを作成（または既存を調整）

purchase_uid (string, unique, required)

payment_email (string, nullable)

amount (int, default 0)

credits (int, default 0)

claimed_user_id (FK->users, nullable)

claimed_at (timestamp, nullable)

expires_at (timestamp, nullable) ※作成から7日でデフォルト期限

payload (json)

timestamps

モデル App\Models\PayzPendingGrant を用意。$fillable と $casts を適切に。

Webhook側（参考）

決済ステータスが paid のイベントを受けたら、purchase_uid をキーに payz_pending_grants を updateOrCreate。

credits は amountToCredits()で計算

payment_plansで登録されているデータを参照して付与クレジットを算出

この時点では付与しない（保留）。

2) ルーティング

GET /charge → App\Http\Controllers\Billing\ChargePickupController@pickup

ルート名：charge.pickup

未ログイン時はログインへリダイレクトし、intended URL にこの /charge?... を保存して復帰。

3) ヘルパー

App\Support\StrEx::normalizeEmail(string|null): ?string を追加。

実装：mb_strtolower(trim($email)) のみ（ドット無視/エイリアス除去はしない）。

必要なら App\Support\StrEx::secureEquals($a,$b) を作り、タイミング攻撃対策に hash_equals を利用。

4) コントローラ ChargePickupController@pickup

実装要件：

クエリから purchase_uid, email を取得。どちらか欠ければエラーで /dashboard へ（flash: “不正なリンクです”）。

認証必須：未ログインなら login に誘導（url.intended を保持）。

payz_pending_grants.purchase_uid = :purchase_uid を検索。なければ /dashboard（“該当の決済が見つかりません”）。

claimed_at 済は弾く（“すでに引き取り済みです”）。

expires_at が過去なら弾く（“引き取り期限を過ぎています”）。

二重照合

normalizeEmail(query.email) と normalizeEmail(pending.payment_email) が一致しなければ弾く（“メール照合に失敗しました（リンク無効）”）。

normalizeEmail(auth()->user()->email) と normalizeEmail(query.email) が一致しなければ弾く（“ログイン中アカウントとメールが一致しません”）。

付与処理（トランザクション）：

users.current_credits を + pending.credits

credit_histories に insert：

user_id, credit=pending.credits, amount=pending.amount,

note='Payz決済（メールリンク引き取り）: '.$pending->payment_email,

granted_at=now(), meta（json）に source='payz-link', purchase_uid を保存

pending.claimed_user_id = auth()->id(), claimed_at = now() に更新

正常時：/dashboard へ（flash: “決済を引き取り、クレジットを付与しました”）。

重要：purchase_uid は一意・推測困難であることを前提。
将来強化として、sig=HMAC_SHA256(purchase_uid|email, PAYZ_LINK_SECRET) の検証も入れられるよう、拡張しやすい構造で書いて。

5) 換算レート

ProcessPayzWebhook::amountToCredits(int $amount): int を実装（既存があれば流用）。

デフォ例：return intdiv($amount, 500);

値は .env / config/billing.php から変更できるように。

6) UX / Blade（簡易）

/dashboard で session('status') を通知表示（Tailwindの成功/警告/エラー）。

失敗時メッセージは上記flashに合わせる。

7) ログ & 監査

重要イベントは info ログ：

charge.pickup.granted user_id=<id> purchase_uid=<id> credits=<n>

charge.pickup.denied reason=<string> purchase_uid=<id> email=<linkEmail> loginEmail=<loginEmail> paymentEmail=<paymentEmail>

Filament で payz_pending_grants を監査表示できるように（任意）。

8) テスト（Feature）

正常系：一致（query.email=payment_email=login.email）→付与・claimed_at更新

異常系：

purchase_uid 不正

claimed_at 済

期限切れ

query.email≠payment_email

login.email≠query.email

各ケースでクレジットが変化しないこと、flash文言が適切であることを検証。

9) 参考 cURL（Webhook後に保留が作られている前提）
open "https://ai.abe-labo.biz/charge?purchase_uid=P_IVM7OFQQ&email=golgokoji@gmail.com"


※ ブラウザで開いた後、未ログインならGoogleでログイン→復帰→付与。

Deliverables

Migration, Model, Route, Controller, Support helper, Config, Logs, Basic tests.

コードは PSR-12、型宣言と early return、例外よりバリデーション優先。

既存コードと衝突しないよう命名を慎重に。コンフィグ値は .env で上書き可。

If anything is unclear, propose the best-practice default and proceed.