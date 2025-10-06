<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('credit_histories', function (Blueprint $table) {
            // typeカラムは不要。systemカラムを使う。
            if (!Schema::hasColumn('credit_histories', 'video_id')) {
                $table->unsignedBigInteger('video_id')->nullable()->after('type')->comment('消費時の動画ID');
            }
        });
    }
    public function down() {
        Schema::table('credit_histories', function (Blueprint $table) {
            if (Schema::hasColumn('credit_histories', 'video_id')) {
                $table->dropColumn('video_id');
            }
            // typeカラムは不要。systemカラムを使う。
        });
    }
};
