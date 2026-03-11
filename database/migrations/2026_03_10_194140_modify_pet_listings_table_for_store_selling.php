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
        Schema::table('pet_listings', function (Blueprint $table) {
            if (Schema::hasColumn('pet_listings', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            
            $table->foreignUuid('user_id')->nullable()->after('id')->constrained('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pet_listings', function (Blueprint $table) {
            if (Schema::hasColumn('pet_listings', 'store_id')) {
                $table->dropForeign(['store_id']);
                $table->dropColumn('store_id');
            }
            
            $table->foreignUuid('user_id')->nullable()->after('id')->constrained('users')->cascadeOnDelete();
        });
    }
};