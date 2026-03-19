<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('menus')->insert([
            [
                'name' => 'Trang chủ',
                'link' => '/',
                'type' => 'custom',
                'parent_id' => 0,
                'sort_order' => 1,
                'table_id' => null,
                'position' => 'mainmenu',
                'created_by' => 1,
                'status' => 1
            ],
            [
                'name' => 'Sản phẩm',
                'link' => '/san-pham',
                'type' => 'page',
                'parent_id' => 0,
                'sort_order' => 2,
                'table_id' => null,
                'position' => 'mainmenu',
                'created_by' => 1,
                'status' => 1
            ],
        ]);
    }
}
