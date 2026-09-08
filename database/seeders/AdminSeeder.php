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
        // Insert into accounts table
        $account = Account::create([
            'group_id' => 1,
            'name' => 'Admin',
            'phone' => '03001234567',
            'account_type' => 'admin',
            'address' => 'Lahore, Pakistan',
            'created_by' => 1,
        ]);

        // Insert into users table
        $user = User::create([
            'group_id' => 1,
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@rideflow.com',
            'password' => Hash::make('admin123'),
        ]);
    }
}
