<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'user1',
                'email' => 'user1@test.com',
                'password' => '123456',
                'created_at' => now(),
                'updated_at' => now(),
                'permission' => 1,
            ],
            [
                'name' => 'user2',
                'email' => 'user2@test.com',
                'password' => '123456',
                'created_at' => now(),
                'updated_at' => now(),
                'permission' => 2,
            ],
            [
                'name' => 'user3',
                'email' => 'user3@test.com',
                'password' => '123456',
                'created_at' => now(),
                'updated_at' => now(),
                'permission' => 3,
            ],
        ]);
    }
}
