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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name',100); // Payment method name, e.g., ABA Bank, Credit Card
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->string('type', 50)->comment('online,cash,card_on_delivery');

            $table->string('status', 10)->default('ACTIVE')->comment('ACTIVE, INACTIVE');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->integer('update_num')->default(0);
            $table->timestampsTz();
            $table->softDeletesTz(); // Adds a nullable `deleted_at` timestamp with timezone
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};