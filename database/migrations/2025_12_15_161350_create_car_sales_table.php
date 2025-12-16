<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_sales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->foreignUuid('car_id')->constrained('cars')->cascadeOnDelete();
            $table->foreignUuid('buyer_id')->constrained('users')->cascadeOnDelete(); 
            $table->decimal('final_price', 12, 2); 
            $table->string('status')->default('requested');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_sales');
    }
};
