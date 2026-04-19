<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user1 = User::create([
            'name' => 'user1',
            'email' => 'user1@test.com',
            'password' => Hash::make('123456'),
        ]);
        $user1->assignRole('admin');

        $user2 = User::create([
            'name' => 'user2',
            'email' => 'user2@test.com',
            'password' => Hash::make('123456'),
        ]);
        $user2->assignRole('manager');

        $user3 = User::create([
            'name' => 'user3',
            'email' => 'user3@test.com',
            'password' => Hash::make('123456'),
        ]);
        $user3->assignRole('user');
    }
}