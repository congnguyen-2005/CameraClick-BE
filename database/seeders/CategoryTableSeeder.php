<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->truncate();

        DB::table('categories')->insert([
            // ===== CHA =====
            [
                'name'        => 'Máy ảnh',
                'slug'        => 'may-anh',
                'image'       => 'category_camera.jpg',
                'parent_id'   => 0,
                'sort_order'  => 1,
                'description' => 'Các dòng máy ảnh chính hãng.',
                'status'      => 1,
                'created_by'  => 1,
                'updated_by'  => null,
            ],
            [
                'name'        => 'Ống kính',
                'slug'        => 'ong-kinh',
                'image'       => 'category_lens.jpg',
                'parent_id'   => 0,
                'sort_order'  => 2,
                'description' => 'Ống kính cho các hệ máy ảnh.',
                'status'      => 1,
                'created_by'  => 1,
                'updated_by'  => null,
            ],
            [
                'name'        => 'Phụ kiện máy ảnh',
                'slug'        => 'phu-kien-may-anh',
                'image'       => 'category_accessories.jpg',
                'parent_id'   => 0,
                'sort_order'  => 3,
                'description' => 'Phụ kiện hỗ trợ chụp ảnh & quay phim.',
                'status'      => 1,
                'created_by'  => 1,
                'updated_by'  => null,
            ],

            // ===== CON ===== (PHẢI ĐỦ CỘT)
            [
                'name'        => 'DSLR',
                'slug'        => 'dslr',
                'image'       => null,
                'parent_id'   => 1,
                'sort_order'  => 1,
                'description' => 'Máy ảnh DSLR chuyên nghiệp.',
                'status'      => 1,
                'created_by'  => 1,
                'updated_by'  => null,
            ],
            [
                'name'        => 'Mirrorless',
                'slug'        => 'mirrorless',
                'image'       => null,
                'parent_id'   => 1,
                'sort_order'  => 2,
                'description' => 'Máy ảnh không gương lật.',
                'status'      => 1,
                'created_by'  => 1,
                'updated_by'  => null,
            ],
            [
                'name'        => 'Compact',
                'slug'        => 'compact',
                'image'       => null,
                'parent_id'   => 1,
                'sort_order'  => 3,
                'description' => 'Máy ảnh nhỏ gọn.',
                'status'      => 1,
                'created_by'  => 1,
                'updated_by'  => null,
            ],
        ]);
    }
}
