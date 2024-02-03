<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

// php artisan test --filter LeadControllerTest
class LeadControllerTest extends TestCase
{
    use RefreshDatabase;

    // php artisan test --filter LeadControllerTest::testReturnsAListOfLeads
    public function testReturnsAListOfLeads()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        Lead::factory()->count(5)->create();
        $response = $this->get('/api/v1/leads');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertJsonCount(5);
        $response->assertJsonStructure([
            '*' => ['id', 'name', 'email', 'created_at', 'updated_at'],
        ]);
    }

    // php artisan test --filter LeadControllerTest::testStore
    public function testStore()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $leadData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone_number' => '12345678901',
        ];

        $response = $this->post('/api/v1/leads', $leadData);

        $response->assertStatus(201);

        $response->assertHeader('Content-Type', 'application/json');

        $this->assertDatabaseHas('leads', $leadData);

        $response->assertJson($leadData);
    }

    // php artisan test --filter LeadControllerTest::testStoreValidation
    public function testStoreValidation()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $invalidLeadData = [
            'name' => '',  // name é obrigatório
            'email' => 'invalid_email',  // email deve ser um email válido
            'phone_number' => 'aaa',  // phone_number deve ter no mínimo 11 caracteres
        ];

        try {
            $this->post('/api/v1/leads', $invalidLeadData);
        } catch (ValidationException $e) {
            $this->assertEquals(422, $e->status);

            $this->assertEquals(['name', 'email', 'phone_number'], array_keys($e->errors()));
            $this->assertArrayHasKey('name', $e->errors());
            $this->assertArrayHasKey('email', $e->errors());
            $this->assertArrayHasKey('phone_number', $e->errors());

            $this->assertEquals(['The name field is required.'], $e->errors()['name']);
            $this->assertEquals(['The email field must be a valid email address.'], $e->errors()['email']);
            $this->assertContains('The phone number field must be a number.',  $e->errors()['phone_number']);
            $this->assertContains('The phone number field must be 11 digits.',  $e->errors()['phone_number']);

            return;
        }

        $this->fail('Expected ValidationException was not thrown.');
    }

    // php artisan test --filter LeadControllerTest::testUpdateLead
    public function testUpdateLead()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $lead = Lead::factory()->create();

        $dataToUpdate = [
            'name' => 'Novo Nome',
            'email' => 'novoemail@example.com',
            'phone_number' => '11123456789',
        ];

        $response = $this->put("/api/v1/leads/{$lead->id}", $dataToUpdate);

        $response->assertStatus(200);

        $this->assertDatabaseHas('leads', $dataToUpdate);

        $response->assertJson($dataToUpdate);
    }

    // php artisan test --filter LeadControllerTest::testUpdateLeadNotFound
    public function testUpdateLeadNotFound()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $dataToUpdate = [
            'name' => 'Novo Nome',
            'email' => 'novoemail@example.com',
            'phone_number' => '11123456789',
        ];

        $response = $this->put("/api/v1/leads/invalid_id", $dataToUpdate);

        $response->assertStatus(404);

        $response->assertJson(['error' => 'Lead not found']);
    }

    // php artisan test --filter LeadControllerTest::testStoreLeadValidation
    public function testStoreLeadValidation()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $lead = Lead::factory()->create();

        $invalidLeadData = [
            'name' => '',
            'email' => 'novoemailexample.com',
            'phone_number' => 'aaaaaaa6789',
        ];

        try {
            $this->put("/api/v1/leads/{$lead->id}", $invalidLeadData);
        } catch (ValidationException $e) {
            $this->assertEquals(422, $e->status);

            $this->assertEquals(['name', 'email', 'phone_number'], array_keys($e->errors()));
            $this->assertArrayHasKey('name', $e->errors());
            $this->assertArrayHasKey('email', $e->errors());
            $this->assertArrayHasKey('phone_number', $e->errors());

            $this->assertEquals(['The name field is required.'], $e->errors()['name']);
            $this->assertEquals(['The email field must be a valid email address.'], $e->errors()['email']);
            $this->assertContains('The phone number field must be a number.',  $e->errors()['phone_number']);
            $this->assertContains('The phone number field must be 11 digits.',  $e->errors()['phone_number']);

            return;
        }

        $this->fail('Expected ValidationException was not thrown.');
    }

    // php artisan test --filter LeadControllerTest::testDestroyLead
    public function testDestroyLead()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $lead = Lead::factory()->create();

        $response = $this->delete("/api/v1/leads/{$lead->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('leads', ['id' => $lead->id]);
    }

    // php artisan test --filter LeadControllerTest::testDestroyLeadNotFound
    public function testDestroyLeadNotFound()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->delete("/api/v1/leads/invalid_id");

        $response->assertStatus(404);

        $response->assertJson(['error' => 'Lead not found']);
    }

    // php artisan test --filter LeadControllerTest::testShowLead
    public function testShowLead()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $lead = Lead::factory()->create(['user_id' => $user->id]);

        $response = $this->get("/api/v1/leads/{$lead->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id', 'name', 'phone_number',
            'created_at', 'updated_at', 'user_id',
        ]);
    }

    // php artisan test --filter LeadControllerTest::testShowNonExistentLead
    public function testShowNonExistentLead()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->delete("/api/v1/leads/invalid_id");

        $response->assertStatus(404);
        $response->assertJson(['error' => 'Lead not found']);
    }
}
