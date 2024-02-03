<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;


// php artisan test --filter AuthControllerTest
class AuthControllerTest extends TestCase
{

    use RefreshDatabase;

    // php artisan test --filter AuthControllerTest::testLoginWithValidCredentials
    public function testLoginWithValidCredentials()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth', [
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Authorized',
                'status' => 200,
                'accessToken' => $response['accessToken'],
            ]);

        $this->assertAuthenticatedAs($user, 'sanctum');
    }

    // php artisan test --filter AuthControllerTest::testLogout
    public function testLogout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;


        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->deleteJson('/api/v1/auth');

        $response->assertStatus(200)->assertJson(['status' => 'Token revoked']);

        $this->assertDatabaseMissing('personal_access_tokens', ['token' => $token]);
    }

      // php artisan test --filter AuthControllerTest::testLoginWithInvalidCredentials
      public function testLoginWithInvalidCredentials()
    {
        $response = $this->postJson('/api/v1/auth', [
            'email' => 'invalid@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(403)
            ->assertJson(['status' => 'Not authorized']);

        $this->assertGuest();
    }
}
