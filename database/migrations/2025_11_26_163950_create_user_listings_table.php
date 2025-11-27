<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_listings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // Assumes standard Laravel 'users' table exists
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('model_id')->constrained('models')->cascadeOnDelete();

            $table->year('year');
            $table->string('condition')->comment('New, Used, etc.');
            $table->decimal('price', 12, 2);
            $table->text('description')->nullable();
            $table->string('status')->default('pending')->comment('pending, approved, rejected, sold');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_listings');
    }
};
