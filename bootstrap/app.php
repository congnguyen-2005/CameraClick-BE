<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        channels: __DIR__ . '/../routes/channels.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    // ->withMiddleware(function (Middleware $middleware): void {

    //     // ĐĂNG KÝ MIDDLEWARE TẠI ĐÂY
    //     $middleware->alias([
    //         'checkAdmin' => \App\Http\Middleware\CheckAdmin::class,
    //     ]);

    // })
    ->withMiddleware(function (Middleware $middleware) {

    // 1. Ép HandleCors chạy ở mức độ toàn cầu (Global)
    $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);

    // 2. ĐĂNG KÝ MIDDLEWARE CỦA BẠN
    $middleware->alias([
        'checkAdmin' => \App\Http\Middleware\CheckAdmin::class,
    ]);

    // 3. Nếu bạn dùng Sanctum, hãy thêm dòng này để hỗ trợ API
    $middleware->statefulApi();

})
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();