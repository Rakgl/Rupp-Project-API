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
	Schema::create('doctors', function (Blueprint $table) {
		$table->uuid('id')->primary();
		$table->foreignUuid('user_id')->constrained('users')->onDelete('cascade'); // Link to the users table
		$table->string('title')->nullable(); // e.g., Dr., Prof.
		$table->string('registration_number')->unique(); // Medical registration number
		$table->text('bio')->nullable();
		$table->string('gender')->nullable(); // male, female, other
		$table->date('date_of_birth')->nullable();
		$table->string('consultation_fee')->nullable()->default(0);
		$table->string('currency_code')->default('USD');
		$table->integer('years_of_experience')->nullable();
		$table->json('qualifications')->nullable(); // Store as JSON or comma-separated
		$table->string('profile_picture_path')->nullable();
		$table->boolean('is_verified')->default(false); // Admin can verify doctor profiles
		$table->boolean('is_available_for_consultation')->default(true);
		$table->enum('availability_status', ['available', 'busy', 'offline'])->default('offline');
		$table->decimal('average_rating', 2, 1)->nullable(); // e.g., 4.5
		$table->foreignUuid('hospital_id')->nullable()->constrained('hospitals')->onDelete('set null');
		$table->string('status')->default('ACTIVE');
		$table->timestamps();
		$table->softDeletes();

		$table->string('created_by')->nullable();
		$table->string('updated_by')->nullable();
		$table->string('deleted_by')->nullable();
	});
}

/**
 * Reverse the migrations.
 */
public function down(): void
{
	Schema::dropIfExists('doctors');
}
};
