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
		Schema::create('appointments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignUuid('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->foreignUuid('hospital_id')->nullable()->constrained('hospitals')->onDelete('set null');
            $table->dateTime('appointment_datetime');
            $table->integer('duration_minutes')->default(30);
            $table->enum('status', ['scheduled', 'confirmed', 'cancelled_by_patient', 'cancelled_by_doctor', 'completed', 'missed', 'rescheduled'])->default('scheduled');
            $table->text('reason_for_visit')->nullable();
            $table->text('doctor_notes')->nullable();
            $table->text('patient_notes')->nullable();
            $table->string('meeting_link')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->string('cancelled_by')->nullable();
            $table->decimal('consultation_fee_charged', 10, 2)->nullable();
            $table->string('payment_status')->default('pending');
            $table->string('payment_gateway_transaction_id')->nullable();
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
        Schema::dropIfExists('appointments');
    }
};
