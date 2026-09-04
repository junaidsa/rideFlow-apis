<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GroupSeeder extends Seeder
{
public function run(): void
{
    DB::table('groups')->insert([
        [
            'id' => 1,
            'name' => 'RideFlow',
            'phone' => '03001234567',
            'address' => 'Lahore, Pakistan',
            'logo' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ],
    ]);
}
}