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
        Schema::create('insurance_providers', function (Blueprint $table) {
			$table->uuid('id')->primary();
            $table->string('name');
            $table->string('policy_prefix')->nullable()->unique(); // e.g., "INS"
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable()->unique();
            $table->text('address')->nullable();
            $table->string('website')->nullable();
            $table->text('coverage_details')->nullable();
            $table->string('status')->default('ACTIVE');
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
        Schema::dropIfExists('insurance_providers');
    }
};
