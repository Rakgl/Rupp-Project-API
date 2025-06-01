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
        Schema::create('static_contents', function (Blueprint $table) {
            $table->uuid('id')->primary();
			$table->json('title');
			$table->json('content');
			$table->string('image')->nullable();
			$table->string('type', 20)->comment('PRIVACY_POLICY, TERMS_AND_CONDITIONS, ABOUT_US');
			$table->string('status', 10)->default('ACTIVE')->comment('ACTIVE, INACTIVE, DELETED');
			$table->string('created_by')->nullable();
			$table->string('updated_by')->nullable();
			$table->integer('update_num')->default(0);
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('static_contents');
    }
};
