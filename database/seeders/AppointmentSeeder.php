<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Service;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stores = Store::all();
        $users = User::all();
        $pets = Pet::all();
        $services = Service::all();
        
        if ($stores->isEmpty() || $users->isEmpty() || $pets->isEmpty() || $services->isEmpty()) {
            return;
        }

        $now = Carbon::now();

        // Generate past appointments (last 30 days)
        for ($i = 0; $i < 20; $i++) {
            $user = $users->random();
            $userPets = $pets->where('user_id', $user->id);
            if ($userPets->isEmpty()) continue;

            $startTime = $now->copy()->subDays(rand(1, 30))->setHour(rand(8, 17))->setMinute(0);
            
            Appointment::create([
                'id' => (string) Str::uuid(),
                'store_id' => $stores->random()->id,
                'user_id' => $user->id,
                'pet_id' => $userPets->random()->id,
                'service_id' => $services->random()->id,
                'start_time' => $startTime,
                'end_time' => $startTime->copy()->addMinutes(60),
                'status' => 'COMPLETED',
                'special_requests' => 'Standard service'
            ]);
        }

        // Generate today's appointments
        for ($i = 0; $i < 5; $i++) {
            $user = $users->random();
            $userPets = $pets->where('user_id', $user->id);
            if ($userPets->isEmpty()) continue;

            $startTime = $now->copy()->setHour(rand(8, 17))->setMinute(0);
            
            Appointment::create([
                'id' => (string) Str::uuid(),
                'store_id' => $stores->random()->id,
                'user_id' => $user->id,
                'pet_id' => $userPets->random()->id,
                'service_id' => $services->random()->id,
                'start_time' => $startTime,
                'end_time' => $startTime->copy()->addMinutes(60),
                'status' => $startTime->isPast() ? 'IN_CARE' : 'CONFIRMED',
                'special_requests' => 'Please handle with care.'
            ]);
        }

        // Generate future appointments
        for ($i = 0; $i < 10; $i++) {
            $user = $users->random();
            $userPets = $pets->where('user_id', $user->id);
            if ($userPets->isEmpty()) continue;

            $startTime = $now->copy()->addDays(rand(1, 14))->setHour(rand(8, 17))->setMinute(0);
            
            Appointment::create([
                'id' => (string) Str::uuid(),
                'store_id' => $stores->random()->id,
                'user_id' => $user->id,
                'pet_id' => $userPets->random()->id,
                'service_id' => $services->random()->id,
                'start_time' => $startTime,
                'end_time' => $startTime->copy()->addMinutes(60),
                'status' => 'PENDING',
                'special_requests' => null
            ]);
        }
    }
}
