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
        Schema::create('prescriptions', function (Blueprint $table) {
			$table->uuid('id')->primary();
            $table->foreignUuid('appointment_id')->constrained('appointments')->onDelete('cascade');
            $table->foreignUuid('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignUuid('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->date('prescription_date');
            $table->text('diagnosis')->nullable();
            $table->text('general_advice')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->foreignUuid('pharmacy_id')->nullable()->constrained('pharmacies')->onDelete('set null');
            $table->enum('status', ['pending_fulfillment', 'fulfilled', 'partially_fulfilled', 'cancelled'])->default('pending_fulfillment');
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('prescriptions');
    }
};
