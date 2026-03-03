<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('pet_id')->constrained('pets')->cascadeOnDelete();
            $table->foreignUuid('service_id')->constrained('services')->cascadeOnDelete();
            
            $table->timestampTz('start_time')->comment('When they drop the pet off');
            $table->timestampTz('end_time')->nullable()->comment('When they pick the pet up');
            
            $table->string('status', 20)->default('PENDING')->comment('PENDING, CONFIRMED, IN_CARE, COMPLETED, CANCELLED');
            $table->text('special_requests')->nullable();
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
