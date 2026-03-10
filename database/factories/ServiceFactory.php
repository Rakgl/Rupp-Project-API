<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->words(2, true);
        $description = $this->faker->sentence();

        return [
            'name' => [
                'en' => ucfirst($name),
                'kh' => 'សេវាកម្ម ' . $name,
                'zh' => '服务 ' . $name,
            ],
            'description' => [
                'en' => $description,
                'kh' => 'ការពិពណ៌នា: ' . $description,
                'zh' => '描述: ' . $description,
            ],
            'price' => $this->faker->randomFloat(2, 5, 100),
            'duration_minutes' => $this->faker->randomElement([15, 30, 45, 60, 90, 120]),
            'image_url' => 'https://picsum.photos/400/300?random=' . $this->faker->unique()->numberBetween(1, 1000),
            'status' => 'ACTIVE',
        ];
    }
}