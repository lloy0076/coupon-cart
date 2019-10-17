<?php

use App\Constants;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        $user = factory(User::class)->make([
            'name' => 'David Lloyd',
            'email' => 'jwickentower@gmail.com',
            'password' => Hash::make('testing123'),
        ]);

        $didSave = $user->save();

        if (!$didSave) {
            throw new Exception("Unable to save user.");
        }

        $adminUser = factory(User::class)->make([
            'name' => 'David Lloyd',
            'email' => 'lloy0076@adam.com.au',
            'password' => Hash::make('testing123'),
        ]);

        $didSave = $adminUser->save();

        if (!$didSave) {
            throw new Exception("Unable to save admin user.");
        }

        $adminUser->assignRole(Constants::ROLE_ADMIN);

        $user2 = factory(User::class)->make([
            'name' => 'Plain User',
            'email' => 'user@example.com',
            'password' => Hash::make('testing123'),
        ]);

        $didSave = $user2->save();

        if (!$didSave) {
            throw new Exception("Unable to save user.");
        }

        $adminUser2 = factory(User::class)->make([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('testing123'),
        ]);

        $didSave = $adminUser2->save();

        if (!$didSave) {
            throw new Exception("Unable to save admin user.");
        }

        $adminUser2->assignRole(Constants::ROLE_ADMIN);
    }
}
