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
		 // Transactions Table (for payments)
		 Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade'); // User who made/received payment
            $table->string('transaction_type');
            $table->foreignUuid('related_id')->nullable();
            $table->string('related_type')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency_code', 3)->default('USD');
            $table->string('payment_gateway')->nullable();
            $table->string('gateway_transaction_id')->nullable()->unique();
            $table->string('status');
            $table->text('notes')->nullable();
            $table->timestamp('transaction_date')->useCurrent();
            $table->timestamps();
            $table->index(['related_id', 'related_type']);

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
