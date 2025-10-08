<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PayzWebhookTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * PayzWebhookのPOST受信テスト
     */
    public function test_payz_webhook_receives_post_and_returns_204()
    {
        $payload = [
            'purchase_uid' => 'P_TEST12345',
            'product_uid' => 'pd_sampleproduct',
            'user_uid' => 'ur_sampleuser',
            'email' => 'test@example.com',
            'status' => 'purchased',
            'name_last' => '山田',
            'name_first' => '太郎',
            'tel' => '09012345678',
            'client_ip' => '127.0.0.1',
            'client_ua' => 'phpunit',
        ];

        $response = $this->postJson('/api/webhooks/payz', $payload);
        $response->assertStatus(204); // No Content
    }
}
