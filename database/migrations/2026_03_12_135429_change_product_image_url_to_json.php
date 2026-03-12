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
        // 1. Temporarily change the column to TEXT to avoid casting issues
        // and allow for update.
        DB::statement('ALTER TABLE products ALTER COLUMN image_url TYPE TEXT USING image_url::text');

        // 2. Update existing string URLs to JSON array strings
        DB::table('products')->whereNotNull('image_url')->update([
            'image_url' => DB::raw("json_build_array(image_url)")
        ]);

        // 3. Finally, change column type to JSON
        DB::statement('ALTER TABLE products ALTER COLUMN image_url TYPE JSON USING image_url::json');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // For reversing, we will convert the JSON array back to a single string (the first element)
        // This is a destructive operation if multiple images were stored.
        DB::statement('ALTER TABLE products ALTER COLUMN image_url TYPE VARCHAR(255) USING (image_url->>0)');
    }
};
