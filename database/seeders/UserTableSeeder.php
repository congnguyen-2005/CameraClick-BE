<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'name'       => 'Admin',
                'email'      => 'admin@example.com',
                'phone'      => '0123456789',
                'username'   => 'admin',
                'password'   => Hash::make('123456'),
                'roles'      => 'admin',
                'avatar'     => null,
                'created_by' => 1,
                'updated_by' => null,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Customer Test',
                'email'      => 'customer@example.com',
                'phone'      => '0987654321',
                'username'   => 'customer',
                'password'   => Hash::make('123456'),
                'roles'      => 'customer',
                'avatar'     => null,
                'created_by' => 1,
                'updated_by' => null,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
