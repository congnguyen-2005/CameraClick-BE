<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductImageTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('product_images')->truncate();

        DB::table('product_images')->insert([
            // ===== Sony A6400 =====
            [
                'product_id' => 1,
                'image'      => 'sony-a6400-1.jpg',
                'alt'        => 'Máy ảnh Sony A6400 góc trước',
                'title'      => 'Sony A6400 Body - Hình 1',
            ],
            [
                'product_id' => 1,
                'image'      => 'sony-a6400-2.jpg',
                'alt'        => 'Máy ảnh Sony A6400 góc sau',
                'title'      => 'Sony A6400 Body - Hình 2',
            ],

            // ===== Canon EOS R50 =====
            [
                'product_id' => 2,
                'image'      => 'canon-eos-r50-1.jpg',
                'alt'        => 'Máy ảnh Canon EOS R50 mặt trước',
                'title'      => 'Canon EOS R50 Body - Hình 1',
            ],
            [
                'product_id' => 2,
                'image'      => 'canon-eos-r50-2.jpg',
                'alt'        => 'Máy ảnh Canon EOS R50 mặt sau',
                'title'      => 'Canon EOS R50 Body - Hình 2',
            ],

            // ===== Lens Sony 18-50mm =====
            [
                'product_id' => 3,
                'image'      => 'sony-18-50mm-1.jpg',
                'alt'        => 'Ống kính Sony 18-50mm',
                'title'      => 'Lens Sony 18-50mm - Hình 1',
            ],
        ]);
    }
}
