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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 50);
            $table->string('email', 100)->nullable();
            $table->string('image', 100)->nullable();
            $table->string('username', 50)->unique();
            $table->string('password');
            $table->string('status', 10)->default('ACTIVE');
            $table->string('role_id')->nullable();
            $table->string('locale', 10)->default('en');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->integer('update_num')->default(0);
            $table->string('fcm_token')->nullable();
            $table->string('platform', 10)->nullable()->comment('IOS, ANDROID');
            $table->string('type', 50)->default('Mobile')->comment('Mobile, Admin, Station Admin, Card');
			$table->string('avatar_fallback_color')->nullable()->comment('blue', 'red');
			$table->string('language')->nullable()->comment('language');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
