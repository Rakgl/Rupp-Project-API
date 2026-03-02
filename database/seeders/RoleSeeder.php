<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon; // Import Carbon for consistent timestamps

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now(); // Get current timestamp once for consistency

        $roles = [
            [
                'id' => Str::uuid()->toString(),
                'name' => "Super Admin",
                'description' => "Full Control over the entire system.",
                'status' => 'ACTIVE',
                'type' => 'admin',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => "Developer",
                'description' => "Full development access and system control.",
                'status' => 'ACTIVE',
                'type' => 'admin',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => "Admin",
                'description' => "Administrative control over most system features.",
                'status' => 'ACTIVE',
                'type' => 'admin',
                'created_at' => $now,
                'updated_at' => $now,
            ],
			[
                'id' => Str::uuid()->toString(),
                'name' => "User",
                'description' => "User only!",
                'status' => 'ACTIVE',
                'type' => 'user',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => "QA",
                'description' => "QA control over most system features.",
                'status' => 'ACTIVE',
                'type' => 'qa',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Insert in chunks to avoid potential issues with very large arrays
        foreach (array_chunk($roles, 50) as $chunk) {
            DB::table('roles')->upsert(
                $chunk, 
                ['name'],
                ['description', 'status', 'type', 'updated_at']
            );
        }

        $this->command->info('Roles seeded successfully.');
    }
}