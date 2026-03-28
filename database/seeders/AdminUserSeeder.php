<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Nguyễn Thành Công Admin',
            'email' => 'admin@cameraclick.com', // Thay bằng email của bạn
            'username' => 'admin',
            'password' => Hash::make('12345678'), // Mật khẩu của bạn
            'phone' => '0987654321',
            'roles' => 'admin', // Hoặc số 1 tùy theo logic role của bạn
            'status' => 1,
        ]);
    }
}