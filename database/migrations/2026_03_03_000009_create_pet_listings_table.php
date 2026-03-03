<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_listings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete()->comment('The seller');
            $table->foreignUuid('pet_id')->constrained('pets')->cascadeOnDelete()->comment('The pet being sold');
            
            $table->string('listing_type', 20)->default('SALE')->comment('SALE, ADOPTION');
            $table->decimal('price', 12, 2)->nullable()->comment('Null if adoption/free');
            $table->text('description')->nullable();
            
            $table->string('status', 20)->default('AVAILABLE')->comment('AVAILABLE, PENDING, SOLD');
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_listings');
    }
};
