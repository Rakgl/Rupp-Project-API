<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->uuid('itemable_id')->nullable()->after('order_id');
            $table->string('itemable_type')->nullable()->after('itemable_id');
        });

        // Migrate existing data if any
        DB::table('order_items')->update([
            'itemable_id' => DB::raw('product_id'),
            'itemable_type' => 'App\Models\Product'
        ]);

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('product_id');
            $table->uuid('itemable_id')->nullable(false)->change();
            $table->string('itemable_type')->nullable(false)->change();
            $table->index(['itemable_id', 'itemable_type']);
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->uuid('product_id')->nullable()->after('order_id');
        });

        DB::table('order_items')->where('itemable_type', 'App\Models\Product')->update([
            'product_id' => DB::raw('itemable_id')
        ]);

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['itemable_id', 'itemable_type']);
            $table->uuid('product_id')->nullable(false)->change();
        });
    }
};
