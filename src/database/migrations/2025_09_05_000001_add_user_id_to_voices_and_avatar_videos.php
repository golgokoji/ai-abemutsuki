<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('voices', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('id');
        });
        Schema::table('avatar_videos', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('voices', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('avatar_videos', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};
