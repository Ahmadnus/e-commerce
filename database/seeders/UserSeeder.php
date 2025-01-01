<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {


        User::create([
            'name' => 'faker',
            'email' => 'faker@gmail.com',
            'password' => Hash::make('ahmad123'),
            'is_admin' => 1,
        ]);


        User::create([
            'name' => 'ahmad',
            'email' => 'ahmad@gmail.com',
            'password' => Hash::make('ahmad123'),
            'is_admin' => 0,
        ]);
    }
}
