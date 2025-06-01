<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->json('question');
            $table->json('answer');
			// $table->string('category')->comment('FAQ, ABOUT, SUPPORT');
            $table->string('platform', 10)->default('MOBILE');
			$table->string('image')->nullable();
            $table->string('status', 10)->default('ACTIVE')->comment('ACTIVE, INACTIVE');
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
        Schema::dropIfExists('faqs');
    }
};
