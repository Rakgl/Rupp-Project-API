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
        
        if ($stores->isEmpty() || $users->isEmpty()) {
            return;
        }

        // Create some dummy Services
        $service1 = Service::create([
            'id' => (string) Str::uuid(),
            'name' => '{"en": "Basic Grooming", "km": "ការសម្អាតទូទៅ"}',
            'description' => '{"en": "Includes bath, brush, and nail trim", "km": "រួមបញ្ចូលការងូតទឹក សិតសក់ និងកាត់ក្រចក"}',
            'price' => 25.00,
            'duration_minutes' => 60,
            'status' => 'ACTIVE'
        ]);

        $service2 = Service::create([
            'id' => (string) Str::uuid(),
            'name' => '{"en": "Full Spa", "km": "ស្ប៉ាពេញលេញ"}',
            'description' => '{"en": "Premium bath, haircut, nail trim, and ear cleaning", "km": "ការងូតទឹកពិសេស កាត់សក់ កាត់ក្រចក និងសម្អាតត្រចៀក"}',
            'price' => 50.00,
            'duration_minutes' => 120,
            'status' => 'ACTIVE'
        ]);

        // Create some dummy Pets
        $user = $users->last();
        $pet1 = Pet::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'name' => 'Buddy',
            'species' => 'Dog',
            'breed' => 'Golden Retriever',
            'weight' => 25.5,
            'date_of_birth' => Carbon::now()->subYears(3)->format('Y-m-d')
        ]);

        $pet2 = Pet::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'name' => 'Luna',
            'species' => 'Cat',
            'breed' => 'Siamese',
            'weight' => 4.2,
            'date_of_birth' => Carbon::now()->subYears(2)->format('Y-m-d')
        ]);

        $services = [$service1, $service2];
        $pets = [$pet1, $pet2];
        $now = Carbon::now();

        // Generate past appointments, today's appointments, and future appointments
        for ($i = -2; $i <= 5; $i++) {
            $appointmentTime = $now->copy()->addDays($i)->setHour(rand(9, 16))->setMinute(0)->setSecond(0);
            
            // To easily demonstrate Dashboard KPI
            if ($i == 0) {
                // Force a couple of appointments specifically for TODAY
                $appointmentTime = $now->copy()->addMinutes(30);
            }

            Appointment::create([
                'id' => (string) Str::uuid(),
                'store_id' => $stores->random()->id,
                'user_id' => $user->id,
                'pet_id' => collect($pets)->random()->id,
                'service_id' => collect($services)->random()->id,
                'start_time' => $appointmentTime,
                'end_time' => $appointmentTime->copy()->addMinutes(60),
                'status' => $appointmentTime->isPast() ? 'COMPLETED' : 'PENDING',
                'special_requests' => 'Handle with care'
            ]);
        }
        
        // Add one more for today
        Appointment::create([
            'id' => (string) Str::uuid(),
            'store_id' => $stores->random()->id,
            'user_id' => $user->id,
            'pet_id' => $pet2->id,
            'service_id' => $service1->id,
            'start_time' => $now->copy()->addHours(2),
            'end_time' => $now->copy()->addHours(3),
            'status' => 'CONFIRMED',
            'special_requests' => null
        ]);
    }
}
