<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            // Rename store_id to user_id if it exists
            if (Schema::hasColumn('pets', 'store_id') && !Schema::hasColumn('pets', 'user_id')) {
                $table->renameColumn('store_id', 'user_id');
            }
        });

        // Drop old foreign key and add new one
        Schema::table('pets', function (Blueprint $table) {
            // Use raw SQL to drop constraint if it exists to be safe in PGSQL
            DB::statement('ALTER TABLE pets DROP CONSTRAINT IF EXISTS pets_store_id_foreign');
            
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('pet_listings', function (Blueprint $table) {
            // Rename store_id to user_id if it exists
            if (Schema::hasColumn('pet_listings', 'store_id') && !Schema::hasColumn('pet_listings', 'user_id')) {
                $table->renameColumn('store_id', 'user_id');
            }
        });

        Schema::table('pet_listings', function (Blueprint $table) {
            DB::statement('ALTER TABLE pet_listings DROP CONSTRAINT IF EXISTS pet_listings_store_id_foreign');

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
        
        // Ensure foreign keys are correct (this might need raw SQL if renaming isn't enough in PGSQL for constraints)
        // But for now let's hope rename handles basic mapping if no explicit fk was name was used.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            if (Schema::hasColumn('pets', 'user_id')) {
                $table->renameColumn('user_id', 'store_id');
            }
        });

        Schema::table('pet_listings', function (Blueprint $table) {
            if (Schema::hasColumn('pet_listings', 'user_id')) {
                $table->renameColumn('user_id', 'store_id');
            }
        });
    }
};
