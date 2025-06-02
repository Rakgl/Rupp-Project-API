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
        Schema::create('patients', function (Blueprint $table) {
			$table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade'); // Link to the users table
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable(); // male, female, other
            $table->string('blood_group')->nullable();
            $table->text('address_line_1')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('country')->nullable();
            $table->string('profile_picture_path')->nullable();
            $table->foreignUuid('insurance_provider_id')->nullable()->constrained('insurance_providers');
            $table->string('insurance_policy_number')->nullable();
            $table->date('insurance_policy_expiry_date')->nullable();
            $table->text('medical_history_summary')->nullable(); // Brief summary
            $table->text('allergies')->nullable(); // Store as JSON or comma-separated
			$table->string('status', 10)->default('ACTIVE');
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
        Schema::dropIfExists('patients');
    }
};
