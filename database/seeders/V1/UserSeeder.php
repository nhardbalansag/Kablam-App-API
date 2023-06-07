<?php

namespace Database\Seeders\V1;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users_data = [
            [
                'first_name' => "admin",
                'last_name' => "admin",
                'number' => "+639214408767",
                'is_verified' => true,
                'birthday' => Carbon::parse('1997-04-01')->format('Y-m-d'),
                'role_id' => 1,
                'user_photo' => null,
                'is_active' => true,
                'login_type' => "app",
                'email' => "admin@email.com",
                'email_verified_at' => now(),
                'password' => Hash::make('admin'),
            ]
        ];

        DB::table('users')->insert($users_data);
    }
}
