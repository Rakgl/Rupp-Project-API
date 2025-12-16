<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete(); 
            
            // Polymorphic Columns: This links the payment to its source (e.g., CarSale or CarLeasing)
            // Use UUIDs because payables (like CarSale) use UUID primary keys
            $table->uuidMorphs('payable');

            $table->decimal('amount', 12, 2);
            $table->string('method')->nullable()->comment('Stripe, PayPal, Bank Transfer');
            $table->string('transaction_id')->nullable()->unique()->comment('ID from the payment gateway');
            $table->enum('status', ['pending', 'success', 'failed', 'refunded'])->default('pending');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
