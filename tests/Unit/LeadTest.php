<?php

namespace Tests\Unit;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// php artisan test --filter LeadTest
class LeadTest extends TestCase
{
    use RefreshDatabase;

    // php artisan test --filter LeadTest::testUserRelationship
    public function testUserRelationship()
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $lead->user);
        $this->assertEquals($user->id, $lead->user->id);
    }
}
