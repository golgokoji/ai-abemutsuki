<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('voices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('script_id');
            $table->string('status')->default('queued'); // queued, processing, succeeded, failed
            $table->string('provider')->default('elevenlabs');
            $table->string('file_path')->nullable();
            $table->string('public_url')->nullable();
            $table->json('provider_response')->nullable();
            $table->timestamps();

            $table->foreign('script_id')->references('id')->on('scripts')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voices');
    }
};
