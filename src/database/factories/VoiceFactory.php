<?php

namespace Database\Factories;

use App\Models\Voice;
use App\Models\User;
use App\Models\Script;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoiceFactory extends Factory
{
    protected $model = Voice::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'script_id' => Script::factory(),
            'status' => 'succeeded',
            'file_path' => null,
            'public_url' => null,
        ];
    }
}
