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
            $table->string('app')->comment('STORE, CUSTOMER, ADMIN, etc.');
            $table->uuid('announcement_id')->nullable();
            $table->string('platform')->comment('IOS, ANDROID');
            $table->string('latest_version', 20);  // Latest app version (e.g., '2.0.1')
            $table->string('min_supported_version', 20)->nullable(); // Minimum supported version
            $table->text('update_url')->nullable();  // URL to the app store for updates
            $table->boolean('force_update')->default(false);  // Indicates if the update is mandatory
            $table->string('title')->nullable(); // Title for the update message
            $table->text('message')->nullable();  // Optional message to display to the user
            $table->timestampsTz();  // 'created_at' and 'updated_at' timestampsTz
            $table->softDeletesTz(); // 'deleted_at'
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
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
