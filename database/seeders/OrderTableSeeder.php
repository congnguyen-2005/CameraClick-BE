<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('orders')->insert([
            [
                'user_id' => 1,
                'name' => 'Nguyen Van A',
                'email' => 'vana@example.com',
                'phone' => '0909000001',
                'address' => 'HCM, Q1',
                'note' => 'Giao giờ hành chính',
                'status' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'name' => 'Tran Thi B',
                'email' => 'thib@example.com',
                'phone' => '0909000002',
                'address' => 'HCM, Q10',
                'note' => 'Kiểm hàng trước khi nhận',
                'status' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'name' => 'Le Van C',
                'email' => 'vanc@example.com',
                'phone' => '0909000003',
                'address' => 'Hà Nội, Cầu Giấy',
                'note' => null,
                'status' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'name' => 'Pham Thi D',
                'email' => 'thid@example.com',
                'phone' => '0909000004',
                'address' => 'Đà Nẵng, Hải Châu',
                'note' => 'Gọi trước khi giao',
                'status' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'name' => 'Ngo Van E',
                'email' => 'vane@example.com',
                'phone' => '0909000005',
                'address' => 'Cần Thơ, Ninh Kiều',
                'note' => 'Thanh toán tiền mặt',
                'status' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
