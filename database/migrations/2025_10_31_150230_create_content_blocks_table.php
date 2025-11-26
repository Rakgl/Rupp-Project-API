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
        Schema::create('content_blocks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->json('title')->nullable();
            $table->json('description')->nullable();
            $table->json('booking_btn')->nullable();
            $table->text('image_path')->nullable();
            $table->string('status', 10)->comment('ACTIVE, INACTIVE , DELETED')->default('ACTIVE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_blocks');
    }
};