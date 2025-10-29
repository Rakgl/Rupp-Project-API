<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This table stores notification channel settings for each store,
     * allowing a store to have multiple notification providers (e.g., Telegram, Slack).
     */
    public function up(): void
    {
        Schema::create('store_notification_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Link to the store
            $table->foreignUuid('store_id')->constrained('stores')->onDelete('cascade');

            $table->enum('provider', ['telegram', 'email', 'facebook'])
                ->comment("The notification service provider: 'telegram', 'email', or 'facebook'");

            $table->string('name', 255)
                ->comment('A friendly name for this configuration, e.g., "Sales Alerts Chat" or "Order Notifications"');


            // Store provider-specific details like bot_token, chat_id, webhook_url, etc.
            $table->json('credentials')
                ->comment('Provider-specific configuration stored as JSON');


            $table->boolean('is_active')
                ->default(true)
                ->comment('Toggle to enable or disable this specific notification channel');


            $table->timestamps();

            // Add indexes for better performance
            $table->index(['store_id', 'provider']);
            $table->index(['store_id', 'is_active']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_notification_settings');
    }
};