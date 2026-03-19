<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TopicTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('topics')->insert([
            [
                'name' => 'Tin tức',
                'slug' => 'tin-tuc',
                'sort_order' => 1,
                'description' => 'Chuyên mục tin tức',
                'created_by' => 1,
                'status' => 1
            ],
            [
                'name' => 'Khuyến mãi',
                'slug' => 'khuyen-mai',
                'sort_order' => 2,
                'description' => 'Chuyên mục khuyến mãi',
                'created_by' => 1,
                'status' => 1
            ],
        ]);
    }
}
