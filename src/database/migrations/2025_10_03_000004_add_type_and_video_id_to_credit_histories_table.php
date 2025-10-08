<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('credit_histories', function (Blueprint $table) {
            // 1) type カラムが無ければ先に作成
            if (!Schema::hasColumn('credit_histories', 'type')) {
                // 既存カラムの並びに合わせて after(...) は調整ください
                // 不明なら after 指定を外しても問題ありません
                $table->string('type', 32)
                    ->default('grant') // 例: grant|consume など
                    ->comment('種別')
                    ->after('amount'); // 位置は環境に合わせて
            }

            // 2) video_id を type の後ろに追加（type ができていれば通ります）
            if (!Schema::hasColumn('credit_histories', 'video_id')) {
                $table->unsignedBigInteger('video_id')
                    ->nullable()
                    ->comment('消費時の動画ID')
                    ->after('type');
            }
        });
    }
    public function down()
    {
        Schema::table('credit_histories', function (Blueprint $table) {
            if (Schema::hasColumn('credit_histories', 'video_id')) {
                $table->dropColumn('video_id');
            }
            // typeカラムは不要。systemカラムを使う。
        });
    }
};
