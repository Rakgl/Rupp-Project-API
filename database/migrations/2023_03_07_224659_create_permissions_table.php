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
        Schema::create('permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('module', 50)->comment('ADMIN, STATION, CHARGING POINT', 'CHARGING CONNECTOR');
            $table->string('name', 50)->comment('CREATE, READ, UPDATE, DELETE');
            $table->string('slug', 50)->comment('admin:create, admin:read, admin:update, admin:delete');
            $table->boolean('developer_only')->comment('1 = True / 2 = False')->default(0);
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
        Schema::dropIfExists('permissions');
    }
};
