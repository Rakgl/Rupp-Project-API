<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('category_id')->constrained('categories')->cascadeOnDelete();
            $table->json('name');
            $table->string('slug')->unique();
            $table->json('description')->nullable();
            $table->json('attributes')->nullable()->comment('Stores Gender, Age, Brand, Color, etc.');
            $table->decimal('price', 12, 2);
            $table->string('image_url')->nullable();
            $table->string('sku', 50)->unique()->nullable()->comment('Barcode or Stock Keeping Unit');
            $table->string('status', 10)->default('ACTIVE')->comment('ACTIVE, INACTIVE');
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
