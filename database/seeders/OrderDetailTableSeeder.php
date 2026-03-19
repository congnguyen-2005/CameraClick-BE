<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderDetailTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('order_details')->truncate();

        DB::table('order_details')->insert([
            [
                'order_id'  => 1,
                'product_id'=> 1, // Máy ảnh Sony A6400
                'price'     => 21000000,
                'qty'       => 1,
                'amount'    => 21000000,
                'discount'  => 0,
            ],
            [
                'order_id'  => 1,
                'product_id'=> 2, // Lens Sony 18-50
                'price'     => 8500000,
                'qty'       => 1,
                'amount'    => 8500000,
                'discount'  => 500000,
            ],
        ]);
    }
}
