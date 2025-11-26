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
        Schema::create('messages', function (Blueprint $table) {
			$table->uuid('id')->primary();
            $table->foreignUuid('conversation_id')->constrained('conversations')->onDelete('cascade');
            $table->foreignUuid('sender_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->json('content');

            // To handle different types of messages, e.g., text, image, file, system message.
            $table->string('type')->default('text');

            $table->timestamp('read_at')->nullable();

			$table->foreignUuid('reply_to_message_id')->nullable()->onDelete('set null');
            
            // For tracking edits
            $table->timestamp('edited_at')->nullable();


            $table->timestamps();
            $table->softDeletes();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();

            $table->index('conversation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};