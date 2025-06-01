<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = config('audit.drivers.database.connection', config('database.default'));
        $table = config('audit.drivers.database.table', 'audits');


        Schema::connection($connection)->create($table, function (Blueprint $table) {
			DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');
			$morphPrefix = config('audit.user.morph_prefix');
			$table->string('id')->default(DB::raw('uuid_generate_v4()'))->primary();

			$table->string($morphPrefix . '_type')->nullable();
			$table->uuid($morphPrefix . '_id')->nullable();
			$table->index([
				$morphPrefix . '_type',
				$morphPrefix . '_id',
			]);

            $table->string('event');

			$table->string('auditable_type');
			$table->uuid('auditable_id');
			$table->index([
				'auditable_type',
				'auditable_id',
			]);

            $table->text('old_values')->nullable();
            $table->text('new_values')->nullable();
            $table->text('url')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 1023)->nullable();
            $table->string('tags')->nullable();
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
        $connection = config('audit.drivers.database.connection', config('database.default'));
        $table = config('audit.drivers.database.table', 'audits');

        Schema::connection($connection)->drop($table);
    }
}
