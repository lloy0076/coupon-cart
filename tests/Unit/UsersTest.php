<?php

namespace Tests\Unit;

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
        $this->assertTrue($userModels instanceof User, "It is a " . User::class);

        $user = $userModels->where('email', 'jwickentower@gmail.com')->first();
        $this->assertNotNull($user, "User jwickentower@gmail.com exists");
    }
}
