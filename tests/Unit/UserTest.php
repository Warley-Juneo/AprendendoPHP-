<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;


// teste unitarios
// php artisan test --filter UserTest
class UserTest extends TestCase
{
    use RefreshDatabase;

    // php artisan test --filter UserTest::testCreateUser
    public function testCreateUser()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    // php artisan test --filter UserTest::testUserAttributes
    public function testUserAttributes()
    {
        $fillableAttributes = ['name', 'email', 'password'];
        $hiddenAttributes = ['password', 'remember_token'];
        $castAttributes = ['email_verified_at' => 'datetime', 'password' => 'hashed'];

        $user = new User();

        $this->assertEquals($fillableAttributes, $user->getFillable());
        $this->assertEquals($hiddenAttributes, $user->getHidden());
        $this->assertEquals($castAttributes, $user->getCasts());
    }
}
