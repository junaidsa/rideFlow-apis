<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert into users table
        $user = User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@rideflow.com',
            'password' => Hash::make('admin123'),
        ]);
    }
}
