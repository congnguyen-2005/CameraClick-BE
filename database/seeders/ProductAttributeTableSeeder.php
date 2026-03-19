<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductAttributeTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('product_attributes')->truncate();

        DB::table('product_attributes')->insert([
            // ===== Product ID 1: Sony A6400 =====
            [
                'product_id'   => 1,
                'attribute_id' => 1, // Brand
                'value'        => 'Sony'
            ],
            [
                'product_id'   => 1,
                'attribute_id' => 2, // Model
                'value'        => 'A6400'
            ],
            [
                'product_id'   => 1,
                'attribute_id' => 3, // Camera Type
                'value'        => 'Mirrorless'
            ],
            [
                'product_id'   => 1,
                'attribute_id' => 4, // Resolution
                'value'        => '24.2 MP'
            ],
            [
                'product_id'   => 1,
                'attribute_id' => 5, // Sensor Type
                'value'        => 'CMOS'
            ],
            [
                'product_id'   => 1,
                'attribute_id' => 6, // Sensor Size
                'value'        => 'APS-C'
            ],
            [
                'product_id'   => 1,
                'attribute_id' => 7, // Lens Mount
                'value'        => 'Sony E'
            ],
            [
                'product_id'   => 1,
                'attribute_id' => 8, // ISO
                'value'        => '100–32000'
            ],
            [
                'product_id'   => 1,
                'attribute_id' => 9, // Video
                'value'        => '4K UHD'
            ],

            // ===== Product ID 2: Canon EOS R10 =====
            [
                'product_id'   => 2,
                'attribute_id' => 1,
                'value'        => 'Canon'
            ],
            [
                'product_id'   => 2,
                'attribute_id' => 2,
                'value'        => 'EOS R10'
            ],
            [
                'product_id'   => 2,
                'attribute_id' => 3,
                'value'        => 'Mirrorless'
            ],
            [
                'product_id'   => 2,
                'attribute_id' => 4,
                'value'        => '24.2 MP'
            ],
            [
                'product_id'   => 2,
                'attribute_id' => 6,
                'value'        => 'APS-C'
            ],
            [
                'product_id'   => 2,
                'attribute_id' => 7,
                'value'        => 'Canon RF'
            ],
            [
                'product_id'   => 2,
                'attribute_id' => 9,
                'value'        => '4K 60fps'
            ],

            // ===== Product ID 3: Lens Sony 18–50mm =====
            [
                'product_id'   => 3,
                'attribute_id' => 1,
                'value'        => 'Sony'
            ],
            [
                'product_id'   => 3,
                'attribute_id' => 3,
                'value'        => 'Lens'
            ],
            [
                'product_id'   => 3,
                'attribute_id' => 7,
                'value'        => 'Sony E'
            ],
            [
                'product_id'   => 3,
                'attribute_id' => 10, // Weight
                'value'        => '116g'
            ],
        ]);
    }
}
