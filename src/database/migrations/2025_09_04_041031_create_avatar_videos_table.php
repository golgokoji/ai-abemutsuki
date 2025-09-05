<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('avatar_videos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('voice_id');     // どの音声から作ったか
            $table->string('status')->default('queued'); // queued, processing, succeeded, failed
            $table->string('provider')->default('heygen');
            $table->string('video_id')->nullable();     // HeyGenのvideo_id
            $table->string('file_url')->nullable();     // 公開/ダウンロードURL
            $table->json('provider_response')->nullable();
            $table->timestamps();

            $table->foreign('voice_id')->references('id')->on('voices')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avatar_videos');
    }
};
