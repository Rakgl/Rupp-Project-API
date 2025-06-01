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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
			$table->json('title');
			$table->json('message');
			$table->string('image')->nullable();
			$table->string('type', 20)->comment('PROMOTION, TRANSACTION, ALERT');
			$table->string('status', 10)->default('UNREAD')->comment('UNREAD, READ');
			$table->uuid('customer_id')->nullable();

			$table->timestampTz('read_at')->nullable();
			$table->boolean('is_read')->default(false);
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
