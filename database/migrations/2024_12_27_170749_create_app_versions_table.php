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
        Schema::create('app_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
			$table->uuid('announcement_id')->nullable();
            $table->string('platform')->comment('IOS, ANDROID');
            $table->string('latest_version', 10);  // Latest app version (e.g., '2.0.1')
            $table->text('update_url')->nullable();  // URL to the app store for updates
            $table->boolean('force_update')->default(false);  // Indicates if the update is mandatory
            $table->text('message')->nullable();  // Optional message to display to the user
            $table->timestampsTz();  // 'created_at' and 'updated_at' timestampsTz
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_versions');
    }
};
