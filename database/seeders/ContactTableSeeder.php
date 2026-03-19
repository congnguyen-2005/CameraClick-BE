<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('contacts')->insert([
            [
                'user_id'    => null,
                'name'       => 'Nguyễn Văn A',
                'email'      => 'vana@example.com',
                'phone'      => '0901234567',
                'content'    => 'Tôi muốn hỏi về sản phẩm A.',
                'reply_id'   => 0,
                'status'     => 1,
                'created_by' => 1,
                'updated_by' => null,
            ],
            [
                'user_id'    => null,
                'name'       => 'Trần Thị B',
                'email'      => 'tranb@example.com',
                'phone'      => '0987654321',
                'content'    => 'Shop có hỗ trợ giao nhanh không?',
                'reply_id'   => 0,
                'status'     => 1,
                'created_by' => 1,
                'updated_by' => null,
            ],
            [
                'user_id'    => 1,
                'name'       => 'Admin',
                'email'      => 'admin@example.com',
                'phone'      => '0909999999',
                'content'    => 'Khách hỏi đơn hàng #1023.',
                'reply_id'   => 2,
                'status'     => 1,
                'created_by' => 1,
                'updated_by' => null,
            ]
        ]);
    }
}
