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
        Schema::create('doctor_availability_slots', function (Blueprint $table) {
			$table->uuid('id')->primary();
            $table->foreignUuid('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->date('slot_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('slot_duration_minutes')->default(30);
            $table->boolean('is_booked')->default(false);
            $table->foreignUuid('booked_by_appointment_id')->nullable()->constrained('appointments')->onDelete('set null');
            $table->timestamps();
            $table->index(['doctor_id', 'slot_date']);
            $table->unique(['doctor_id', 'slot_date', 'start_time']);
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
        Schema::dropIfExists('doctor_availability_slots');
    }
};
