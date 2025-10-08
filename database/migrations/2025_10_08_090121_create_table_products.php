<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ----- BRANDS -----
        Schema::create('brands', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // ----- CATEGORIES (e.g. EV, Gasoline, Hybrid) -----
        Schema::create('categories', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // ----- LISTING TYPES (e.g. Car, Bike, Boat) -----
        Schema::create('listing_types', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // ----- VEHICLE LISTINGS -----
        Schema::create('listings', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            
            // All foreign keys referencing a UUID primary key must use foreignUuid()
            $table->foreignUuid('seller_id')->constrained('users')->cascadeOnDelete();
            
            // Foreign keys referencing UUIDs in new tables
            $table->foreignUuid('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->foreignUuid('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignUuid('listing_type_id')->nullable()->constrained('listing_types')->nullOnDelete();

            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->string('location')->nullable();
            $table->enum('condition', ['new', 'used'])->default('used');
            $table->enum('status', ['pending', 'approved', 'rejected', 'sold'])->default('pending');

            $table->timestamps();
        });

        // ----- SHOP PRODUCT TYPES (e.g. Car Parts, Accessories) -----
        Schema::create('shop_product_types', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // ----- SHOP PRODUCTS (orderable small items) -----
        Schema::create('shop_products', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->integer('stock')->default(0);
            
            // Foreign keys referencing UUIDs
            $table->foreignUuid('seller_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->foreignUuid('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignUuid('type_id')->nullable()->constrained('shop_product_types')->nullOnDelete();
            
            $table->string('image_url')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // ----- ADDRESSES -----
        Schema::create('addresses', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete(); // CHANGED to foreignUuid
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('country');
            $table->string('phone_number');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        // ----- ORDERS -----
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->foreignUuid('buyer_id')->constrained('users')->cascadeOnDelete(); 
            $table->decimal('total_price', 12, 2);
            $table->foreignUuid('shipping_address_id')->nullable()->constrained('addresses')->nullOnDelete();
            
            $table->string('shipping_phone')->nullable(); 
            $table->enum('status', ['pending', 'paid', 'shipped', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });

        // ----- ORDER ITEMS -----
        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->foreignUuid('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignUuid('shop_product_id')->constrained('shop_products')->cascadeOnDelete();
            
            $table->integer('quantity');
            $table->decimal('price', 12, 2);
            $table->timestamps();
        });

        // ----- PAYMENTS -----
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->foreignUuid('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete(); 
            
            $table->decimal('amount', 12, 2);
            $table->string('method')->nullable();
            $table->enum('purpose', ['order', 'listing_fee', 'feature_fee'])->default('order');
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('shop_products');
        Schema::dropIfExists('shop_product_types');
        Schema::dropIfExists('listings');
        Schema::dropIfExists('listing_types');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('brands');
    }
};