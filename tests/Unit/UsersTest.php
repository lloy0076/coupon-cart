<?php

namespace Tests\Unit;

use App\Constants;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use DatabaseMigrations;

    private $setup = true;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testProduct()
    {
        $this->seed();

        $userModels = new User();
        $this->assertInstanceOf(User::class, $userModels, "It is a user class");

        $user = $userModels->where('email', 'jwickentower@gmail.com')->first();
        $this->assertNotNull($user, "User exists");

        $adminUser = $userModels->where('email', 'lloy0076@adam.com.au')->first();
        $this->assertNotNull($adminUser, "User exists");
        $this->assertTrue($adminUser->hasRole(Constants::ROLE_ADMIN));

        $user2 = $userModels->where('email', 'user@example.com')->first();
        $this->assertNotNull($user2, "User exists");

        $adminUser2 = $userModels->where('email', 'admin@example.com')->first();
        $this->assertNotNull($adminUser2, "User exists");
        $this->assertTrue($adminUser2->hasRole(Constants::ROLE_ADMIN));
    }
}
