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
        Schema::create('announcements', function (Blueprint $table) {
            $table->uuid('id')->primary();
			$table->json('title');
			$table->json('message');
			$table->string('type', 20)->comment('PROMOTION, SYSTEM_ALERT, UPDATE');
			$table->timestampTz('scheduled_at')->nullable();
			$table->string('status', 10)->default('PENDING')->comment('PENDING, SENT, CANCELLED');
			$table->string('image')->nullable();
			$table->timestampTz('sent_at')->nullable();
			$table->string('sent_by')->nullable();
			$table->string('created_by')->nullable();
			$table->string('updated_by')->nullable();
			$table->integer('update_num')->default(0);
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
