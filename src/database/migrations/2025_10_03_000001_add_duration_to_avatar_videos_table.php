<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('avatar_videos', function (Blueprint $table) {
            $table->integer('duration')->default(0)->comment('動画の長さ（秒）');
        });
    }
    public function down() {
        Schema::table('avatar_videos', function (Blueprint $table) {
            $table->dropColumn('duration');
        });
    }
};
