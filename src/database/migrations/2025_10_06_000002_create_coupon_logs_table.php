<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('coupon_logs', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32);
            $table->string('email', 255);
            $table->integer('credit')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();
            $table->index(['code', 'email']);
        });
    }
    public function down() {
        Schema::dropIfExists('coupon_logs');
    }
};
