<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSaleTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('product_sales')->insert([
            [
                'name' => 'Giảm giá đầu năm',
                'product_id' => 1,
                'price_sale' => 30990000,
                'date_begin' => '2025-01-01 00:00:00',
                'date_end'   => '2025-01-15 23:59:59',
                'created_by' => 1,
                'updated_by' => null,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sale Tết Nguyên Đán',
                'product_id' => 1,
                'price_sale' => 29990000,
                'date_begin' => '2025-02-01 00:00:00',
                'date_end'   => '2025-02-08 23:59:59',
                'created_by' => 1,
                'updated_by' => null,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Khuyến mãi mùa hè',
                'product_id' => 2,
                'price_sale' => 25990000,
                'date_begin' => '2025-06-01 00:00:00',
                'date_end'   => '2025-06-30 23:59:59',
                'created_by' => 1,
                'updated_by' => null,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Giảm giá cuối tuần',
                'product_id' => 3,
                'price_sale' => 4990000,
                'date_begin' => '2025-03-10 00:00:00',
                'date_end'   => '2025-03-12 23:59:59',
                'created_by' => 1,
                'updated_by' => null,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Flash Sale 24h',
                'product_id' => 3,
                'price_sale' => 4790000,
                'date_begin' => '2025-04-01 00:00:00',
                'date_end'   => '2025-04-01 23:59:59',
                'created_by' => 1,
                'updated_by' => 1,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
