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
        Schema::create('password_reset_o_t_p_s', function (Blueprint $table) {
            $table->uuid('id')->primary();
			$table->string('transaction_code');
			$table->string('phone', 15);
			$table->string('country_code', 5);
			// $table->string('otp', 6);
			$table->string('status', 10)->comment('PENDING, EXPIRED, VERIFIED, FAILED');
			$table->timestampTz('expired_at');
			$table->integer('attempts')->default(0);
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_o_t_p_s');
    }
};
