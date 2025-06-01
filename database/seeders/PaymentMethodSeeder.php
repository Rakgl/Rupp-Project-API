<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		DB::table('payment_methods')->insert([
			[
				'id' => Str::uuid(),
				'name' => "KHQR",
				'description' => 'Scan to pay with any banking app',
				'type' => 'BANK',
				'status' => 'ACTIVE',
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now(),
				'created_by' => null,
				'updated_by' => null,
				'update_num' => 0
			],
		]);
    }
}
