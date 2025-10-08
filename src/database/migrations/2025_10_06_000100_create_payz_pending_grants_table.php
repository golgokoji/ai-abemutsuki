<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payz_pending_grants', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_uid')->unique();
            $table->string('product_uid');
            $table->string('payment_email')->nullable();
            $table->integer('amount')->default(0);
            $table->integer('credit')->default(0);
            $table->foreignId('claimed_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('claimed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payz_pending_grants');
    }
};
