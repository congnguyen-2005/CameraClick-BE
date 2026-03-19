<?php


use Illuminate\Support\Facades\Route;


use Illuminate\Support\Facades\Mail;
// Route::get('/', function () {
//     return view('products');
// });

Route::any('/api/test', function () {
    return response('OK', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
});



Route::get('/test-email', function () {
    try {
        Mail::raw('Đây là email kiểm tra từ Laravel Camera Shop', function ($message) {
            $message->to('dia_chi_email_cua_ban@gmail.com') // <--- Thay email của bạn vào đây
                    ->subject('Test Connection');
        });
        return 'Gửi mail THÀNH CÔNG! Cấu hình của bạn đã đúng.';
    } catch (\Exception $e) {
        return 'Gửi mail THẤT BẠI. Lỗi chi tiết: ' . $e->getMessage();
    }
});