<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100);
            $table->string('code', 10);
            $table->string('iso', 10);
            $table->string('flag', 50)->nullable();
            $table->boolean('default')->comment('0 = False / 1 = True')->default(0);
			$table->string('status', 10)->comment('ACTIVE, INACTIVE , DELETED')->default('ACTIVE');
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locales');
    }
};
