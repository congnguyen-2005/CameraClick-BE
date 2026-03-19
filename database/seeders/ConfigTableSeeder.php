<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('configs')->insert([
            'site_name' => 'My Website',
            'email' => 'admin@example.com',
            'phone' => '0123456789',
            'hotline' => '0987654321',
            'address' => 'Hà Nội',
            'status' => 1,
        ]);
    }
}
