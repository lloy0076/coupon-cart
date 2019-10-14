<?php

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
    }
}
