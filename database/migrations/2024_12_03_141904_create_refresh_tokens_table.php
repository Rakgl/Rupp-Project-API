<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('refresh_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('name');
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->string('token', 64)->unique();
            $table->timestampTz('expires_at');
            $table->boolean('revoked')->default(false);
            $table->timestampsTz();

            // New indexes for refresh token operations
            $table->index(['user_id', 'revoked', 'expires_at']);
            $table->index(['token', 'revoked', 'expires_at']);

            // For active tokens, use a simpler partial index
            $table->index(['token', 'user_id'])->where('revoked', false);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refresh_tokens');
    }
};
