<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('model_id')->constrained('models')->cascadeOnDelete();
            $table->foreignUuid('body_type_id')->nullable()->constrained('body_types')->nullOnDelete();

            // --- INVENTORY MANAGEMENT ---
            $table->unsignedInteger('stock_quantity')->default(1)->comment('Number of identical units.');
            $table->string('status')->default('available')->comment('Indicates if active for listing.');

            // Basic car info
            $table->year('year');
            $table->decimal('price', 12, 2)->nullable()->comment('Base selling price.');
            $table->integer('seat')->nullable();
            $table->string('engine')->nullable();
            $table->integer('door')->nullable();

            $table->string('fuel_type')->comment('EV, Gasoline, Hybrid, Diesel');
            $table->string('condition')->comment('New or Used');
            $table->string('transmission')->comment('Manual or Automatic');

            // Leasing options
            $table->decimal('lease_price_per_month', 10, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
