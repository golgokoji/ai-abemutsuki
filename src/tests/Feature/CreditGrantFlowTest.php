<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\CreditHistory;
use App\Models\PaymentPlan;
use Illuminate\Support\Str;

class CreditGrantFlowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_credit_is_not_granted_twice_for_same_payment()
    {
        // 決済プラン作成
        PaymentPlan::create([
            'name' => 'テストプラン',
            'product_uid' => 'pd_fuxs2lgeyrzl5jat',
            'amount' => 1000,
            'credit' => 10,
            'is_active' => true,
        ]);

        // ユーザー作成
        $user = User::factory()->create([
            'email' => 'golgokoji@gmail.com',
            'password' => bcrypt('password123'),
        ]);

        // Webhook受信（pending保存）
        $purchaseUid = 'P_NOU2NKJQ_' . Str::random(5);
        $this->postJson('/api/webhooks/payz', [
            'purchase_uid' => $purchaseUid,
            'product_uid' => 'pd_fuxs2lgeyrzl5jat',
            'user_uid' => 'ur_nhkp4j9qvp4hyqom',
            'email' => $user->email,
            'status' => 'purchased',
            'name_last' => 'ueda',
            'name_first' => 'koji',
            'tel' => '09058116238',
        ])->assertNoContent();

        // 1回目: クレジット付与
        $this->actingAs($user)
            ->post('/charge', [
                'purchase_uid' => $purchaseUid,
                'email' => $user->email,
            ])
            ->assertRedirect('/dashboard');

        $user->refresh();
        $credit1 = $user->credit_balance;
        $this->assertTrue($credit1 > 0);

        // 2回目: 同じ決済情報で再度付与しようとする
        $response2 = $this->actingAs($user)
            ->post('/charge', [
                'purchase_uid' => $purchaseUid,
                'email' => $user->email,
            ]);
        if (!in_array($response2->getStatusCode(), [201,301,302,303,307,308])) {
            dump('2回目chargeレスポンス:', $response2->getContent());
        }
    $response2->assertStatus(404);

        $user->refresh();
        $credit2 = $user->credit_balance;
        $this->assertEquals($credit1, $credit2, '2回目の付与でクレジット残高が増えていないこと');

        // 履歴も1件のみ
        $this->assertEquals(1, CreditHistory::where('user_id', $user->id)->where('order_id', $purchaseUid)->count());
    }

    /** @test */
    public function test_payz_credit_grant_flow()
    {
        // 0. 決済プラン（product_uid一致・有効）を作成
        PaymentPlan::create([
            'name' => 'テストプラン',
            'product_uid' => 'pd_fuxs2lgeyrzl5jat',
            'amount' => 1000,
            'credit' => 10,
            'is_active' => true,
        ]);
        // 1. ユーザー作成
        $user = User::factory()->create([
            'email' => 'golgokoji@gmail.com',
            'password' => bcrypt('password123'),
        ]);

        // 2. Payz Webhook受信（pending保存）
        $purchaseUid = 'P_NOU2NKJQ_' . Str::random(5);
        $response = $this->postJson('/api/webhooks/payz', [
            'purchase_uid' => $purchaseUid,
            'product_uid' => 'pd_fuxs2lgeyrzl5jat',
            'user_uid' => 'ur_nhkp4j9qvp4hyqom',
            'email' => $user->email,
            'status' => 'purchased',
            'name_last' => 'ueda',
            'name_first' => 'koji',
            'tel' => '09058116238',
        ]);
        $response->assertNoContent();

        // 3. メール記載のリンク（/charge?purchase_uid=...&email=...）にアクセス
        $this->get('/charge?purchase_uid=' . $purchaseUid . '&email=' . $user->email)
            ->assertRedirect('/login'); // 未ログインならログイン画面へ

        // 4. ログインして再度アクセス（確認画面表示）
        $response = $this->actingAs($user)
            ->get('/charge?purchase_uid=' . $purchaseUid . '&email=' . $user->email);
        if ($response->getStatusCode() !== 200) {
            dump('charge画面レスポンス:', $response->getContent());
        }
        if ($response->getStatusCode() !== 200) {
            $msg = "charge画面が200で返っていません。status=" . $response->getStatusCode() . "\nレスポンス内容: " . $response->getContent();
            $this->fail($msg);
        }
        $response->assertSee('クレジット付与確認');

        // 5. 確認ボタンを押してクレジット付与
        $this->actingAs($user)
            ->post('/charge', [
                'purchase_uid' => $purchaseUid,
                'email' => $user->email,
            ])
            ->assertRedirect('/dashboard');

        // 6. ユーザーのクレジット残高が増えていること
        $user->refresh();
        $this->assertTrue($user->credit_balance > 0);

        // 7. クレジット履歴にデータがあること
        $this->assertDatabaseHas('credit_histories', [
            'user_id' => $user->id,
            'order_id' => $purchaseUid,
        ]);
    }
}
