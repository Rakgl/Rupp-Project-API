<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('species', 50)->comment('Dog, Cat, Bird, etc.');
            $table->string('breed', 100)->nullable();
            $table->decimal('weight', 5, 2)->nullable()->comment('Weight in KG');
            $table->date('date_of_birth')->nullable();
            $table->string('image_url')->nullable();
            $table->text('medical_notes')->nullable();
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
