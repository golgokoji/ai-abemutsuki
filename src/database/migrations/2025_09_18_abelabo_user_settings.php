<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('abelabo_user_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('name');
            $table->string('email');
            $table->string('tel')->nullable();
            // 必要に応じて追加カラム（例: 決済ID、APIキー等）
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('abelabo_user_settings');
    }
};
