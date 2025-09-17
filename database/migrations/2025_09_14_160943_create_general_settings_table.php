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
        Schema::create('general_settings', function (Blueprint $table) {
			$table->uuid('id')->primary();
            $table->string('key')->unique()->comment('The machine-readable key for the setting.');
            $table->string('name')->comment('A human-readable name for the setting.');
            $table->text('value')->nullable()->comment('The value of the setting.');
            $table->string('type')->default('string')->comment('Input type for the admin panel: string, text, boolean, etc.');
            $table->string('group')->default('Default')->index()->comment('A name to group related settings in the UI.');
            $table->text('description')->nullable()->comment('A helpful explanation of the setting.');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_settings');
    }
};