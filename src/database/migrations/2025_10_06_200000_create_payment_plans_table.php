<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // プラン名
            $table->string('product_uid')->unique(); // Payz商品ID
            $table->integer('amount'); // 金額
            $table->integer('credit'); // 付与クレジット
            $table->boolean('is_active')->default(true); // 有効/無効
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_plans');
    }
};
