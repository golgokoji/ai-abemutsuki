<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Voice;
use App\Models\Script;

class VoiceControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 音声一覧画面が表示される()
    {
        $user = User::factory()->create();
        $script = Script::factory()->create([
            'user_id' => $user->id,
            'title' => 'テストタイトル',
            'text' => 'テスト本文'
        ]);
        Voice::factory()->create(['user_id' => $user->id, 'script_id' => $script->id, 'status' => 'succeeded']);

        $response = $this->actingAs($user)->get(route('voices.index'));
        $response->assertStatus(200);
        $response->assertSee('音声一覧');
        $response->assertSee('テストタイトル');
    }
}
