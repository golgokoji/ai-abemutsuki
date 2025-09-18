<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('credit_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('order_id')->unique(); // 注文ID（infotop/payzのID）
            $table->integer('amount'); // 金額（円）
            $table->integer('credit'); // 付与したポイント（クレジット数）
            $table->string('system'); // 決済システム名（infotop/payz_salon/payz_direct等）
            $table->timestamp('granted_at')->useCurrent(); // 付与日時
            $table->string('note')->nullable(); // 備考・理由等
            $table->timestamps();
            // 外部キー制約は設定しない
        });
    }

    public function down()
    {
        Schema::dropIfExists('credit_histories');
    }
};
