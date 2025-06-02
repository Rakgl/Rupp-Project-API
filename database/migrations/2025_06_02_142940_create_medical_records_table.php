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
        Schema::create('medical_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignUuid('uploaded_by_user_id')->constrained('users')->onDelete('cascade'); // This already tracks who uploaded
            $table->foreignUuid('appointment_id')->nullable()->constrained('appointments')->onDelete('set null');
            $table->string('record_type');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            // User tracking fields (created_by is somewhat redundant with uploaded_by_user_id, but for consistency)
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
        Schema::dropIfExists('medical_records');
    }
};
