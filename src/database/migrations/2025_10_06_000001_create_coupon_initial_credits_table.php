<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('coupon_initial_credits', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->nullable();
            $table->integer('credit')->default(0);
            $table->boolean('is_active')->default(true);
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_until')->nullable();
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('coupon_initial_credits');
    }
};
