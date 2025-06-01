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
        Schema::create('user_logins', function (Blueprint $table) {
            $table->uuid('id')->primary();
			$table->string('type', 10)->comment('login, logout');
			$table->uuid('user_id')->index();
			$table->string('ip_address');
			$table->string('browser')->nullable()->comment('Chrome, Firefox, Safari, Edge');
			$table->timestampTz('login_at')->nullable();
			$table->timestampTz('logout_at')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_logins');
    }
};
