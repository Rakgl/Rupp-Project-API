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
        Schema::create('banners', function (Blueprint $table) {
			$table->uuid('id')->primary();
            $table->string('name', 255);
            $table->string('image_url_mobile', 1024);
            $table->string('image_url_tablet', 1024)->nullable();
            $table->string('title_text', 255)->nullable();
            $table->string('subtitle_text', 500)->nullable();
            $table->string('cta_text', 100)->nullable(); // Assuming CTA might not always be present
            $table->string('cta_action_type', 50)->comment('e.g., DEEP_LINK, EXTERNAL_URL, SERVICE_CATEGORY, DOCTOR_PROFILE');
            $table->string('cta_action_value', 1024);
            $table->integer('priority')->default(0)->index();
            $table->string('status', 20)->default('INACTIVE')->index()->comment('e.g., ACTIVE, INACTIVE, SCHEDULED, ARCHIVED');
            $table->timestamp('start_date')->nullable()->index();
            $table->timestamp('end_date')->nullable()->index();
            $table->string('display_locations', 255)->nullable()->comment('e.g., HOME_SCREEN, APPOINTMENTS_SECTION. Consider JSON or separate table for multiple locations.');
            
            $table->string('language_code', 10)->nullable()->index();
            $table->string('region_code', 10)->nullable()->index();

            $table->unsignedInteger('impression_count')->default(0);
            $table->unsignedInteger('click_count')->default(0);
            
            $table->foreignUuid('created_by')->nullable();
            $table->foreignUuid('updated_by')->nullable();
			
			$table->softDeletes(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
