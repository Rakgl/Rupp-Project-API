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
        Schema::create('stores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('logo_url')->nullable();
            $table->text('address');
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('zip_code');
            $table->string('country')->default('DefaultCountry');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('phone_number')->nullable();
            $table->string('telegram')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('license_number')->nullable()->unique();
            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();
            $table->boolean('is_24_hours')->default(false);
            $table->boolean('delivers_product')->default(false);
            $table->text('delivery_details')->nullable(); // e.g., radius, fees
            $table->decimal('average_rating', 2, 1)->default(0);
            $table->unsignedInteger('review_count')->default(0);
            $table->boolean('is_highlighted')->default(false);
            $table->boolean('is_top_choice')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->string('status')->default('ACTIVE');
            $table->timestamps();
            $table->softDeletes();

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
        Schema::dropIfExists('stores');
    }
};