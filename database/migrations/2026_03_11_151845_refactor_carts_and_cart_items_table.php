<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Clear existing carts, cart_items, and favorites data before schema change 
        // to avoid "Not null violation" for itemable_type and favorable_type.
        // It's safe to do this since we removed the Stores entirely and previous cart items are invalid.
        DB::table('cart_items')->truncate();
        DB::table('carts')->truncate();
        DB::table('favorites')->truncate();

        // 1. Remove store_id from carts
        Schema::table('carts', function (Blueprint $table) {
            if (Schema::hasColumn('carts', 'store_id')) {
                DB::statement('ALTER TABLE carts DROP CONSTRAINT IF EXISTS carts_store_id_foreign');
                $table->dropColumn('store_id');
            }
        });

        // 2. Make cart_items polymorphic
        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'product_id')) {
                DB::statement('ALTER TABLE cart_items DROP CONSTRAINT IF EXISTS cart_items_product_id_foreign');
                $table->dropColumn('product_id');
            }

            if (!Schema::hasColumn('cart_items', 'itemable_type')) {
                $table->uuidMorphs('itemable');
            }
        });

        // 3. Make favorites polymorphic & drop store_id
        Schema::table('favorites', function (Blueprint $table) {
            if (Schema::hasColumn('favorites', 'store_id')) {
                DB::statement('ALTER TABLE favorites DROP CONSTRAINT IF EXISTS favorites_store_id_foreign');
                $table->dropColumn('store_id');
            }

            if (Schema::hasColumn('favorites', 'product_id')) {
                DB::statement('ALTER TABLE favorites DROP CONSTRAINT IF EXISTS favorites_product_id_foreign');
                $table->dropColumn('product_id');
            }

            if (!Schema::hasColumn('favorites', 'favorable_type')) {
                $table->uuidMorphs('favorable');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Down method omitted for brevity since these are destructive schema changes
        // in a local dev environment.
    }
};
