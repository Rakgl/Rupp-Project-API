<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_inventory', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('stock_quantity')->default(0);
            $table->timestampsTz();
            
            $table->unique(['store_id', 'product_id']); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_inventory');
    }
};
