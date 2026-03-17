<?php

namespace Tests\Feature\Api\V1\Mobile;

use App\Models\User;
use App\Models\UserRegisterOTP;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        config(['services.mekong_sms' => [
            'url' => 'http://test.url',
            'user' => 'test_user',
            'pass' => 'test_pass',
            'sender' => 'test_sender',
            'text1' => 'test_text1',
            'text2' => 'test_text2',
            'expiry' => 'test_expiry',
        ]]);

        // Set env variables directly because OTPHelper uses env() instead of config()
        putenv('MEKONG_SMS_URL=http://test.url');
        putenv('MEKONG_SMS_USER=test_user');
        putenv('MEKONG_SMS_PASS=test_pass');
        putenv('MEKONG_SMS_SENDER=test_sender');
        putenv('MEKONG_SMS_TEXT1=test_text1');
        putenv('MEKONG_SMS_TEXT2=test_text2');
        putenv('MEKONG_SMS_EXPIRY=60');
    }

    public function test_full_registration_flow()
    {
        $this->withoutExceptionHandling();
        // 1. Verify Phone Number (Sends OTP)
        Http::fake([
            '*/otp/sendotp.aspx*' => Http::response("0:TEST_TRANS_123", 200),
        ]);

        $response = $this->postJson('/api/v1/mobile/auth/verify-phone-number', [
            'phone' => '123456789',
            'country_code' => '855',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'transaction_code' => 'TEST_TRANS_123',
            ]);

        $this->assertDatabaseHas('user_register_o_t_p_s', [
            'phone' => '123456789',
            'transaction_code' => 'TEST_TRANS_123',
            'status' => 'PENDING',
        ]);

        // 2. Verify OTP
        // The verify-otp endpoint uses OTPHelper::verify which calls Mekong SMS verifyotp.aspx
        Http::fake([
            '*/otp/verifyotp.aspx*' => Http::response("0:Verified", 200),
        ]);

        $response = $this->postJson('/api/v1/mobile/auth/verify-otp', [
            'otp' => '123456',
            'transaction_code' => 'TEST_TRANS_123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'OTP verified successfully.',
            ]);

        $this->assertDatabaseHas('user_register_o_t_p_s', [
            'transaction_code' => 'TEST_TRANS_123',
            'status' => 'VERIFIED',
        ]);

        // 3. Register Account
        $response = $this->postJson('/api/v1/mobile/auth/register', [
            'phone' => '123456789',
            'name' => 'Test User',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Account registered successfully.',
            ]);

        $this->assertDatabaseHas('users', [
            'phone' => '123456789',
            'name' => 'Test User',
            'type' => 'Mobile',
        ]);

        $this->assertNotNull($response->json('data.access_token'));
    }

    public function test_cannot_verify_already_registered_phone()
    {
        User::factory()->create([
            'phone' => '123456789',
            'status' => 'ACTIVE',
        ]);

        $response = $this->postJson('/api/v1/mobile/auth/verify-phone-number', [
            'phone' => '123456789',
            'country_code' => '855',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => false,
                'message' => 'Phone number already registered.',
            ]);
    }
}
