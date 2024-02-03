<?php

namespace Tests\Feature;

use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

// php artisan test --filter UserControllerTest
class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    // php artisan test --filter UserControllerTest::testStore
    public function testStore()
    {
        $validData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => '12345678',
        ];

        $response = $this->post('/api/v1/users', $validData);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    // php artisan test --filter UserControllerTest::testStoreValidation
    public function testStoreValidation()
    {
        $this->withoutExceptionHandling();

        $invalidData = [
            'name' => '',
            'email' => 'invalid_email',
            'password' => '123',
        ];

        try {
            $this->post('/api/v1/users', $invalidData);
        } catch (ValidationException $e) {
            $this->assertEquals(422, $e->status);

            $this->assertEquals(['name', 'email', 'password'], array_keys($e->errors()));
            $this->assertArrayHasKey('name', $e->errors());
            $this->assertArrayHasKey('email', $e->errors());
            $this->assertArrayHasKey('password', $e->errors());

            $this->assertEquals(['The name field is required.'], $e->errors()['name']);
            $this->assertEquals(['The email field must be a valid email address.'], $e->errors()['email']);
            $this->assertContains('The password field must be at least 8 characters.',  $e->errors()['password']);

            return;
        }

        $this->fail('Expected ValidationException was not thrown.');
    }

    // php artisan test --filter UserControllerTest::testGetUserProfileAuthenticated
    public function testGetUserProfileAuthenticated()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->get('/api/v1/users/profile');

        $response->assertStatus(200);
        $response->assertJson($user->toArray());
    }

    // php artisan test --filter UserControllerTest::testReturnsAListOfUsers
    public function testReturnsAListOfUsers()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        User::factory(4)->create();


        $response = $this->get('/api/v1/users');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertJsonCount(5);
        $response->assertJsonStructure([
            '*' => ['id', 'name', 'email', 'created_at', 'updated_at'],
        ]);
    }

    // php artisan test --filter UserControllerTest::testShowUser
    public function testShowUser()
    {

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->get("/api/v1/users/{$user->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id', 'name', 'email',
            'created_at', 'updated_at',
        ]);
    }

    // php artisan test --filter UserControllerTest::testShowNonExistentUser
    public function testShowNonExistentUser()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->get("/api/v1/users/nonexistent");

        $response->assertStatus(404);
        $response->assertJson(['error' => 'User not found']);
    }

    // php artisan test --filter UserControllerTest::testUpdateUser
    public function testUpdateUser()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $data = [
            'name' => 'Novo Nome',
            'email' => 'novoemail@example.com',
            'password' => 'novasenha123',
        ];

        $response = $this->put("/api/v1/users/{$user->id}", $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }

    // php artisan test --filter UserControllerTest::testUpdateUserNotFound
    public function testUpdateUserNotFound()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $dataToUpdate = [
            'name' => 'Novo Nome',
            'email' => 'novoemail@example.com',
            'password' => 'novasenha123',
        ];

        $response = $this->put("/api/v1/users/nonexistent", $dataToUpdate);
        $response->assertStatus(404);

        $response->assertJson(['error' => 'User not found']);
    }

    // php artisan test --filter UserControllerTest::testUpdateUserValidation
    public function testUpdateUserValidation()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $invalidData = [
            'name' => '',
            'email' => 'invalid_email',
            'password' => 'short',
        ];

        $response = $this->json('PUT', "/api/v1/users/{$user->id}", $invalidData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    // php artisan test --filter UserControllerTest::testDestroyLead
    public function testDestroyLead()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $lead = User::factory()->create();

        $response = $this->delete("/api/v1/users/{$lead->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('leads', ['id' => $lead->id]);
    }

    // php artisan test --filter UserControllerTest::testDestroyLeadNotFound
    public function testDestroyLeadNotFound()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->delete("/api/v1/users/invalid_id");

        $response->assertStatus(404);

        $response->assertJson(['error' => 'User not found']);
    }
}
