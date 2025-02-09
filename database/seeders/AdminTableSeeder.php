<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Capservation Admin',
                'username' => 'admin',
                'members' => json_encode([]),
                'program' => null,
                'year_section' => null,
                'position' => null,
                'capstone_adviser' => null, 
                'instructor_id' => null, 
                'user_type' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
