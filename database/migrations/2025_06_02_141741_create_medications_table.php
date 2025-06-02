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
        Schema::create('medications', function (Blueprint $table) {
			$table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('generic_name')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('strength')->nullable();
            $table->string('form')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_prescription_required')->default(true);
			$table->string('status', 10)->default('ACTIVE');
            $table->timestamps();
            // User tracking fields
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
        Schema::dropIfExists('medications');
    }
};
