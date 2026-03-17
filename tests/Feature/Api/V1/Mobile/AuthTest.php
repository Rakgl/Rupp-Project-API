<?php

namespace Tests\Feature\Api\V1\Mobile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_success_with_existing_user()
    {
        $user = User::factory()->create([
            'phone' => '123456789',
            'password' => Hash::make('password123'),
            'status' => 'ACTIVE',
        ]);

        $response = $this->postJson('/api/v1/mobile/auth/login', [
            'phone' => '123456789',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Login successfully.',
            ]);

        $this->assertNotNull($response->json('data.access_token'));
    }

    public function test_login_fails_for_non_existent_user()
    {
        $response = $this->postJson('/api/v1/mobile/auth/login', [
            'phone' => '987654321',
            'password' => 'newpassword123',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Incorrect phone number or password.',
            ]);

        $this->assertDatabaseMissing('users', [
            'phone' => '987654321',
        ]);
    }

    public function test_login_fails_with_incorrect_password()
    {
        $user = User::factory()->create([
            'phone' => '123456789',
            'password' => Hash::make('password123'),
            'status' => 'ACTIVE',
        ]);

        $response = $this->postJson('/api/v1/mobile/auth/login', [
            'phone' => '123456789',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Incorrect phone number or password.',
            ]);
    }
}
