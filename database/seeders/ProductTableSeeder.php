<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
DB::table('products')->delete();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        DB::table('products')->insert([
            // ===== MÁY ẢNH =====
            [
                'category_id' => 1,
                'name'        => 'Sony A6400 Body',
                'slug'        => Str::slug('Sony A6400 Body'),
                'thumbnail'   => 'sony-a6400.jpg',
                'content'     => 'Máy ảnh mirrorless Sony A6400 cảm biến APS-C 24.2MP, quay video 4K.',
                'description' => 'Sony A6400 – lựa chọn lý tưởng cho vlog và nhiếp ảnh.',
                'price_buy'   => 21000000,
                'status'      => 1,
                'created_by'  => 1,
                'updated_by'  => null,
            ],
            [
                'category_id' => 1,
                'name'        => 'Canon EOS R50 Body',
                'slug'        => Str::slug('Canon EOS R50 Body'),
                'thumbnail'   => 'canon-eos-r50.jpg',
                'content'     => 'Canon EOS R50 nhỏ gọn, cảm biến APS-C, quay 4K.',
                'description' => 'Máy ảnh mirrorless Canon cho người mới.',
                'price_buy'   => 18500000,
                'status'      => 1,
                'created_by'  => 1,
                'updated_by'  => null,
            ],

            // ===== ỐNG KÍNH =====
            [
                'category_id' => 2,
                'name'        => 'Lens Sony E 18-50mm F3.5-5.6',
                'slug'        => Str::slug('Lens Sony E 18-50mm F3.5-5.6'),
                'thumbnail'   => 'sony-18-50mm.jpg',
                'content'     => 'Ống kính kit Sony E 18-50mm cho mirrorless APS-C.',
                'description' => 'Lens đa dụng cho chụp hàng ngày.',
                'price_buy'   => 8500000,
                'status'      => 1,
                'created_by'  => 1,
                'updated_by'  => null,
            ],

            // ===== PHỤ KIỆN =====
            [
                'category_id' => 3,
                'name'        => 'Tripod Benro T660',
                'slug'        => Str::slug('Tripod Benro T660'),
                'thumbnail'   => 'benro-t660.jpg',
                'content'     => 'Chân máy ảnh Benro T660 chắc chắn, gọn nhẹ.',
                'description' => 'Tripod cho máy ảnh và quay video.',
                'price_buy'   => 1200000,
                'status'      => 1,
                'created_by'  => 1,
                'updated_by'  => null,
            ],
            [
                'category_id' => 3,
                'name'        => 'Pin Sony NP-FW50',
                'slug'        => Str::slug('Pin Sony NP-FW50'),
                'thumbnail'   => 'sony-np-fw50.jpg',
                'content'     => 'Pin chính hãng Sony NP-FW50 cho máy ảnh Sony.',
                'description' => 'Pin dự phòng cho máy ảnh Sony.',
                'price_buy'   => 1500000,
                'status'      => 1,
                'created_by'  => 1,
                'updated_by'  => null,
            ],
        ]);
    }
}
