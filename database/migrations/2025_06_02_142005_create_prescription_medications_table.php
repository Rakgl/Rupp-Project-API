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
        Schema::create('prescription_medications', function (Blueprint $table) {
			$table->uuid('id')->primary();
            $table->foreignUuid('prescription_id')->constrained('prescriptions')->onDelete('cascade');
            $table->foreignUuid('medication_id')->constrained('medications')->onDelete('cascade');
            $table->string('dosage');
            $table->string('frequency');
            $table->string('duration');
            $table->integer('quantity_prescribed')->nullable();
            $table->text('instructions')->nullable();
            $table->timestamps();
            $table->unique(['prescription_id', 'medication_id']);
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
        Schema::dropIfExists('prescription_medications');
    }
};
